<?php

namespace Sebastienheyd\BoilerplateMediaManager\Models;

use Carbon\Carbon;
use Illuminate\Filesystem\FilesystemAdapter;

class Directory
{
    private $directory;
    private $storage;

    /**
     * File constructor.
     *
     * @param string            $directory
     * @param FilesystemAdapter $storage
     */
    public function __construct($directory, FilesystemAdapter $storage)
    {
        $this->directory = $directory;
        $this->storage = $storage;
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
