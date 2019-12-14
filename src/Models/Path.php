<?php

namespace Sebastienheyd\BoilerplateMediaManager\Models;

use Cache;
use Storage;

class Path
{
    protected $path = '/';
    protected $storage;
    protected $cacheKey;

    public function __construct($path = '/')
    {
        $this->path = $this->getRelativePath($path);
        $this->storage = Storage::disk('public');
        $this->cacheKey = md5($this->path);
    }

    /**
     * Get current path or file path.
     *
     * @param string $name
     *
     * @return string
     */
    public function path($name = '', $path = null)
    {
        $path = $path ? $this->getRelativePath($path) : $this->path;

        if (empty($name)) {
            return $path;
        }

        return ($path === '/' ? '' : $path).'/'.str_replace(['..', '/'], '', $name);
    }

    /**
     * Return path as array.
     *
     * @return array
     */
    public function breadcrumb()
    {
        if ($this->path === '/') {
            return [];
        }

        $chunks = explode('/', ltrim($this->path, '/'));

        $result = [];
        $path = '';

        foreach ($chunks as $chunk) {
            $path = $path.'/'.$chunk;

            $result[] = [
                'path' => ltrim($path, '/'),
                'name' => $chunk,
            ];
        }

        return $result;
    }

    /**
     * Return parent path.
     *
     * @return bool|string
     */
    public function parent()
    {
        $bc = $this->breadcrumb();

        if (empty($bc)) {
            return false;
        }

        array_pop($bc);

        $path = '';

        if (!empty($bc)) {
            $last = end($bc);
            $path = $last['path'];
        }

        return $this->formatDirectories([$path])[0];
    }

    /**
     * Return list of files and directories in current path.
     *
     * @return array
     */
    public function ls($type = 'all')
    {
        if (Cache::has($this->cacheKey.$type)) {
            return Cache::get($this->cacheKey.$type);
        }

        $directories = $this->storage->directories($this->path);
        $files = $this->storage->files($this->path);

        if (config('boilerplate.mediamanager.hide_thumbs_dir')) {
            $directories = collect($directories)->filter(function ($directory) {
                return !preg_match('#^'.config('boilerplate.mediamanager.thumbs_dir').'#', $directory);
            })->toArray();
        }

        $result = $this->formatDirectories($directories)->merge($this->formatFiles($files));
        $result = $this->filterMedia($result, $type)->all();

        Cache::forever($this->cacheKey.$type, $result);

        return $result;
    }

    /**
     * Filter media collection.
     *
     * @param \Illuminate\Support\Collection $collection
     * @param string                         $type
     *
     * @return mixed
     */
    private function filterMedia($collection, $type)
    {
        return $collection->filter(function ($value) use ($type) {
            foreach (config('boilerplate.mediamanager.filter') as $str) {
                if (preg_match('#^'.$str.'$#', $value['name'])) {
                    return false;
                }
            }

            if ($value['isDir'] === true) {
                return true;
            }

            switch ($type) {
                case 'file':
                    return !in_array($value['type'], ['image', 'video']);
                    break;

                case 'image':
                    return $value['type'] === 'image';
                    break;

                case 'video':
                    return $value['type'] === 'video';
                    break;

                default:
                    return true;
            }
        });
    }

    /**
     * Format an array of files.
     *
     * @param array $files
     *
     * @return \Illuminate\Support\Collection
     */
    private function formatFiles($files = [])
    {
        $files = array_map(function ($file) {
            $file = new File($file, $this->storage);
            $file->generateThumb();

            return $file->toArray();
        }, $files);

        return collect($files);
    }

    /**
     * Format an array of directories.
     *
     * @param array $dirs
     *
     * @return \Illuminate\Support\Collection
     */
    private function formatDirectories($dirs = [])
    {
        $dirs = array_map(function ($dir) {
            $dir = new Directory($dir, $this->storage);

            return $dir->toArray();
        }, $dirs);

        return collect($dirs);
    }

    /**
     * Create a new folder in the current path.
     *
     * @param $name
     */
    public function newFolder($name)
    {
        $path = rtrim($this->path, '/').'/'.trim($name, '/');
        $this->storage->makeDirectory($path);
        $this->clearCache();
    }

    /**
     * Rename a folder or media in the current path.
     *
     * @param string $name
     * @param string $newName
     */
    public function rename($name, $newName)
    {
        $this->storage->move($this->path($name), $this->path($newName));

        if ($this->exists('thumb_'.$name)) {
            $this->storage->move($this->path('thumb_'.$name), $this->path('thumb_'.$newName));
        }

        $this->clearCache();
    }

    /**
     * Move a folder or media to the given path.
     *
     * @param string $name
     * @param string $destinationPath
     */
    public function move($name, $destinationPath)
    {
        $this->storage->move($this->path($name), $this->path($name, $destinationPath));

        if ($this->exists('thumb_'.$name)) {
            $name = '/thumb_'.$name;
            $this->storage->move($this->path($name), $this->path($name, $destinationPath));
        }

        $this->clearCache();
        $this->clearCache($destinationPath);
    }

    /**
     * Store file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string                        $fileName
     *
     * @return string
     */
    public function upload($file, $fileName = null)
    {
        if ($fileName === null) {
            $fileName = $file->getClientOriginalName();
        }

        $this->storage->putFileAs($this->path, $file, $fileName);
        $this->clearCache();

        return $this->getFullPath($this->path.'/'.$fileName);
    }

    /**
     * Delete a folder or a file in the current path.
     *
     * @param $name
     *
     * @return bool
     */
    public function delete($name)
    {
        $path = $this->path($name);
        $fullPath = $this->getFullPath($path);

        if (!is_readable($fullPath)) {
            return false;
        }

        if (is_file($fullPath)) {
            if ($this->exists('thumb_'.$name)) {
                $this->storage->delete($this->path('thumb_'.$name));
            }

            $this->storage->delete($path);
        }

        if (is_dir($fullPath)) {
            $this->storage->deleteDirectory($path);
            $this->clearCache($path);
        }

        $this->clearCache();

        return true;
    }

    /**
     * Get relative path from root.
     *
     * @param $path
     *
     * @return mixed|string
     */
    private function getRelativePath($path)
    {
        $path = str_replace([route('mediamanager.index', [], false), '..'], '', $path);

        return empty($path) ? '/' : $path;
    }

    /**
     * Check if current path exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function exists($name = null)
    {
        $path = $this->path;
        if ($name !== null) {
            $path .= '/'.$name;
        }

        return file_exists($this->getFullPath($path));
    }

    /**
     * Get full path for a given relative path.
     *
     * @param string $name
     *
     * @return string
     */
    public function getFullPath($name)
    {
        return $this->storage->getDriver()->getAdapter()->applyPathPrefix($name);
    }

    /**
     * Clear current path cache.
     *
     * @param string $path
     */
    public function clearCache($path = null)
    {
        $key = $path ? md5($path) : $this->cacheKey;

        foreach (['all', 'file', 'image', 'media', 'video'] as $type) {
            Cache::forget($key.$type);
        }
    }
}
