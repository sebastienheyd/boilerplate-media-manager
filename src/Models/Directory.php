<?php

namespace Sebastienheyd\BoilerplateMediaManager\Models;

use Carbon\Carbon;
use Storage;

class Directory
{
    private $directory;
    private $storage;

    /**
     * File constructor.
     *
     * @param string            $directory
     */
    public function __construct($directory)
    {
        $this->directory = $directory;
        $this->storage = Storage::disk('public');
        $this->pathinfo = pathinfo($this->getFullPath());
        $this->path = rtrim(preg_replace('#'.$this->pathinfo['basename'].'$#', '', $this->directory), '/');
    }

    /**
     * Rename current directory.
     *
     * @param string $newName
     */
    public function rename($newName)
    {
        if ($this->storage->exists('thumbs'.$this->directory)) {
            $this->storage->move('thumbs'.$this->directory, 'thumbs'.$this->path.'/'.$newName);
        }

        $this->storage->move($this->directory, $this->path.'/'.$newName);
    }

    /**
     * Move current directory.
     *
     * @param string $destinationPath
     */
    public function move($destinationPath)
    {
        $destinationPath = rtrim($destinationPath, '/').'/'.$this->pathinfo['basename'];

        if ($this->storage->exists('thumbs'.$this->directory)) {
            $this->storage->move('thumbs'.$this->directory, 'thumbs'.$destinationPath);
        }

        $this->storage->move($this->directory, $destinationPath);
    }

    /**
     * Delete current directory.
     */
    public function delete()
    {
        if ($this->storage->exists('thumbs'.$this->directory)) {
            $this->storage->deleteDirectory('thumbs'.$this->directory);
        }

        $this->storage->deleteDirectory($this->directory);
    }

    /**
     * Get full path for a given relative path.
     *
     * @return mixed
     */
    public function getFullPath()
    {
        return $this->storage->getDriver()->getAdapter()->applyPathPrefix($this->directory);
    }

    /**
     * Return data as array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'download' => '',
            'isDir'    => true,
            'type'     => 'folder',
            'name'     => basename($this->directory),
            'size'     => '-',
            'link'     => route('mediamanager.index', ['path' => $this->directory], false),
            'url'      => $this->storage->url($this->directory),
            'time'     => $this->getFileChangeTime(),
        ];
    }

    /**
     * Return modification time for a given file.
     *
     * @return false|string
     */
    public function getFileChangeTime()
    {
        return Carbon::createFromTimestamp(filectime($this->getFullPath()))
            ->isoFormat(__('boilerplate::date.YmdHis'));
    }
}
