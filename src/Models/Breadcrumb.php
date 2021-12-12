<?php

namespace Sebastienheyd\BoilerplateMediaManager\Models;

use Storage;

class Breadcrumb
{
    protected $path = '/';
    protected $storage;

    public function __construct($path = '/')
    {
        $this->path = $path;
        $this->storage = Storage::disk('public');
    }

    /**
     * Return path as array.
     *
     * @return array
     */
    public function items()
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
        $bc = $this->items();

        if (empty($bc)) {
            return false;
        }

        array_pop($bc);

        $path = '';

        if (! empty($bc)) {
            $last = end($bc);
            $path = $last['path'];
        }

        return $this->formatDirectories([$path])[0];
    }

    /**
     * Format an array of directories.
     *
     * @param  array  $dirs
     * @return \Illuminate\Support\Collection
     */
    private function formatDirectories($dirs = [])
    {
        $dirs = array_map(function ($dir) {
            $dir = new Directory($dir);

            return $dir->toArray();
        }, $dirs);

        return collect($dirs);
    }
}
