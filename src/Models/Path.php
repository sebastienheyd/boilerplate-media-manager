<?php

namespace Sebastienheyd\BoilerplateMediaManager\Models;

use Carbon\Carbon;
use Storage;
use File;

class Path
{
    protected $mce = false;
    protected $path = '/';
    protected $storage;

    public function __construct($path = '/', $mce = false)
    {
        $this->mce = $mce;
        $this->path = $this->getRelativePath($path);
        $this->storage = Storage::disk('public');
    }

    /**
     * Return path as array
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
                'name' => $chunk
            ];
        }

        return $result;
    }

    /**
     * Return current path
     *
     * @return string
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * Return parent path
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
     * Return list of files and directories in current path
     *
     * @return array
     */
    public function ls($type = 'all')
    {
        $directories = $this->storage->directories($this->path);
        $files = $this->storage->files($this->path);

        if(config('mediamanager.hide_thumbs_dir')) {
            $directories = collect($directories)->filter(function($directory) {
                return !preg_match('#^'.config('mediamanager.thumbs_dir').'#', $directory);
            })->toArray();
        }

        $result = $this->formatDirectories($directories)
            ->merge($this->formatFiles($files))
            ->filter(function ($value) use ($type) {

                if (preg_match('#^thumb_#', $value['name'])) {
                    return false;
                }

                if ($value['isDir'] === false && $type === 'image' && $value['type'] !== 'image') {
                    return false;
                }

                return !in_array($value['name'], config('mediamanager.filter'));
            });

        return $result->all();
    }

    /**
     * Format an array of files
     *
     * @param array $files
     *
     * @return \Illuminate\Support\Collection
     */
    private function formatFiles($files = [])
    {
        $files = array_map(function ($file) {
            return [
                'download' => '',
                'icon'     => $this->getIcon($file),
                'type'     => $this->detectFileType($file),
                'name'     => basename($file),
                'isDir'    => false,
                'size'     => $this->getFilesize($file),
                'link'     => route('mediamanager.'.($this->mce == true ? 'mce' : 'index'), ['path' => $file], false),
                'url'      => $this->storage->url($file),
                'time'     => $this->getFileChangeTime($file),
            ];
        }, $files);
        return collect($files);
    }

    /**
     * Format an array of directories
     *
     * @param array $dirs
     *
     * @return \Illuminate\Support\Collection
     */
    private function formatDirectories($dirs = [])
    {
        $dirs = array_map(function ($dir) {
            return [
                'download' => '',
                'isDir'    => true,
                'type'     => 'folder',
                'name'     => basename($dir),
                'size'     => '-',
                'link'     => route('mediamanager.'.($this->mce == true ? 'mce' : 'index'), ['path' => $dir], false),
                'url'      => $this->storage->url($dir),
                'time'     => $this->getFileChangeTime($dir),
            ];
        }, $dirs);
        return collect($dirs);
    }

    /**
     * Get icon for a given file
     *
     * @param $file
     *
     * @return mixed
     */
    public function getIcon($file)
    {
        $type = $this->detectFileType($file);
        $icons = config('mediamanager.icons');
        return $icons[$type];
    }

    /**
     * Create a new folder in the current path
     *
     * @param $name
     *
     * @return mixed
     */
    public function newFolder($name)
    {
        $path = rtrim($this->path, '/').'/'.trim($name, '/');
        return $this->storage->makeDirectory($path);
    }

    /**
     * Rename a folder or media in the current path
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
    }

    /**
     * Store files
     *
     * @param $files
     *
     * @return bool
     */
    public function upload($file)
    {
        $this->storage->putFileAs($this->path, $file, $file->getClientOriginalName());
        return $this->getFullPath($this->path.'/'.$file->getClientOriginalName());
    }

    /**
     * Delete a folder or a file in the current path
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

        return true;
    }

    /**
     * Get relative path from root
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
     * Check if current path exists
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
        $path = $this->getFullPath($path);
        return file_exists($path);
    }

    /**
     * Get full path for a given relative path
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
     * Return type for a given file
     *
     * @param $file
     *
     * @return bool|int|mixed|string
     */
    public function detectFileType($file)
    {
        $extension = File::extension($file);
        foreach (config('mediamanager.filetypes') as $type => $regex) {
            if (preg_match("/^($regex)$/i", $extension) !== 0) {
                return $type;
            }
        }
        return 'file';
    }

    /**
     * Return size for a given file
     *
     * @param $file
     *
     * @return string
     */
    public function getFilesize($file)
    {
        $bytes = filesize($this->getFullPath($file));
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2).' '.$units[$i];
    }

    /**
     * Return modification time for a given file
     *
     * @param $file
     *
     * @return false|string
     */
    public function getFileChangeTime($file)
    {
        return Carbon::createFromTimestamp(filectime($this->getFullPath($file)))
            ->isoFormat(__('boilerplate::date.YmdHis'));
    }
}
