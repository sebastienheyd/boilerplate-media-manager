<?php

namespace Sebastienheyd\BoilerplateMediaManager\Models;

use Cache;
use Storage;

class Path
{
    protected $mce = false;
    protected $path = '/';
    protected $storage;
    protected $cacheKey;

    public function __construct($path = '/', $mce = false)
    {
        $this->mce = $mce;
        $this->path = $this->getRelativePath($path);
        $this->storage = Storage::disk('public');
        $this->cacheKey = md5($this->path.intval($mce));
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
     * Return current path.
     *
     * @return string
     */
    public function path()
    {
        return $this->path;
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
        if (Cache::has($this->cacheKey."_$type")) {
            return Cache::get($this->cacheKey."_$type");
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

        Cache::forever($this->cacheKey."_$type", $result);

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
            if ($value['isDir'] === false) {
                if (preg_match('#^thumb_#', $value['name'])) {
                    return false;
                }

                switch ($type) {
                    case 'file':
                        if ($value['type'] === 'image') {
                            return false;
                        }
                        break;

                    case 'image':
                        if ($value['type'] !== 'image') {
                            return false;
                        }
                        break;

                    case 'media':
                    case 'video':
                        if ($value['type'] !== 'video') {
                            return false;
                        }
                        break;
                }
            }

            return !in_array($value['name'], config('boilerplate.mediamanager.filter'));
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

            return $file->toArray($this->mce);
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

            return $dir->toArray($this->mce);
        }, $dirs);

        return collect($dirs);
    }

    /**
     * Create a new folder in the current path.
     *
     * @param $name
     *
     * @return mixed
     */
    public function newFolder($name)
    {
        $path = rtrim($this->path, '/').'/'.trim($name, '/');
        $result = $this->storage->makeDirectory($path);
        $this->clearCache();

        return $result;
    }

    /**
     * Rename a folder or media in the current path.
     *
     * @param string $name
     * @param string $newName
     *
     * @return mixed
     */
    public function rename($name, $newName)
    {
        $path = rtrim($this->path, '/').'/'.trim($name, '/');
        $dest = rtrim($this->path, '/').'/'.trim($newName, '/');
        $this->storage->move($path, $dest);

        if ($this->exists('thumb_'.$name)) {
            $pathThumb = rtrim($this->path, '/').'/thumb_'.trim($name, '/');
            $destThumb = rtrim($this->path, '/').'/thumb_'.trim($newName, '/');
            $this->storage->move($pathThumb, $destThumb);
        }

        $this->clearCache();
    }

    /**
     * Move a folder or media to the given path.
     *
     * @param string $name
     * @param string $destinationPath
     *
     * @return mixed
     */
    public function move($name, $destinationPath)
    {
        $this->clearCache();
        $this->clearCache($destinationPath);
        $name = trim($name, '/');
        $path = rtrim($this->path, '/').'/'.$name;
        $this->storage->move($path, rtrim($destinationPath, '/').'/'.$name);
        if ($this->exists('thumb_'.$name)) {
            $pathThumb = rtrim($this->path, '/').'/thumb_'.$name;
            $this->storage->move($pathThumb, rtrim($destinationPath, '/').'/thumb_'.$name);
        }
    }

    /**
     * Store file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string                        $fileName
     *
     * @return bool
     */
    public function upload($file, $fileName = null)
    {
        $this->clearCache();

        if ($fileName === null) {
            $fileName = $file->getClientOriginalName();
        }

        $this->storage->putFileAs($this->path, $file, $fileName);

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
        $path = $this->path.'/'.str_replace(['..', '/'], '', $name);
        $fullPath = $this->getFullPath($path);

        if (!is_readable($fullPath)) {
            return false;
        }

        if (is_file($fullPath)) {
            if ($this->exists('thumb_'.$name)) {
                $this->storage->delete($this->path.'/thumb_'.$name);
            }

            $this->storage->delete($path);
        }

        if (is_dir($fullPath)) {
            $this->storage->deleteDirectory($path);
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
        $path = str_replace(route('mediamanager.mce', [], false), '', $path);
        $path = str_replace(route('mediamanager.index', [], false), '', $path);

        if (empty($path)) {
            $path = '/';
        }

        return $path;
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
     * @return mixed
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
        $key = $path ? md5($path.intval($this->mce)) : $this->cacheKey;

        foreach (['all', 'file', 'image', 'media', 'video'] as $type) {
            Cache::forget($key."_$type");
        }
    }
}
