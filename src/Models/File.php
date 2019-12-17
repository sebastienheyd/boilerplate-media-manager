<?php

namespace Sebastienheyd\BoilerplateMediaManager\Models;

use Carbon\Carbon;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\File as BaseFile;
use Intervention\Image\Facades\Image;

class File extends BaseFile
{
    private $file;
    private $storage;

    /**
     * File constructor.
     *
     * @param string            $file
     * @param FilesystemAdapter $storage
     */
    public function __construct($file, FilesystemAdapter $storage)
    {
        $this->file = $file;
        $this->storage = $storage;
    }

    /**
     * Get full path for a given relative path.
     *
     * @return mixed
     */
    public function getFullPath()
    {
        return $this->storage->getDriver()->getAdapter()->applyPathPrefix($this->file);
    }

    /**
     * Automatically generate thumb for image files if not exists.
     */
    public function generateThumb()
    {
        $fullPath = $this->getFullPath();
        $fInfo = pathinfo($fullPath);

        if (preg_match('#^thumb_#', $fInfo['basename'])) {
            return;
        }

        $ext = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'tif'];
        $destFile = $fInfo['dirname'].'/thumb_'.$fInfo['basename'];

        if (in_array(strtolower($fInfo['extension']), $ext) && !is_file($destFile)) {
            Image::make($fullPath)->fit(140)->save($destFile, 75);
        }
    }

    /**
     * Return date as array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'download' => '',
            'icon'     => $this->getIcon(),
            'type'     => $this->detectFileType(),
            'name'     => basename($this->file),
            'isDir'    => false,
            'size'     => $this->getFilesize(),
            'link'     => route('mediamanager.index', ['path' => $this->file], false),
            'url'      => $this->storage->url($this->file),
            'time'     => $this->getFileChangeTime(),
        ];
    }

    /**
     * Get icon for a given file.
     *
     * @return mixed
     */
    public function getIcon()
    {
        $type = $this->detectFileType();
        $icons = config('boilerplate.mediamanager.icons');

        return $icons[$type];
    }

    /**
     * Return type for a given file.
     *
     * @return bool|int|mixed|string
     */
    public function detectFileType()
    {
        $extension = self::extension($this->file);
        foreach (config('boilerplate.mediamanager.filetypes') as $type => $regex) {
            if (preg_match("/^($regex)$/i", $extension) !== 0) {
                return $type;
            }
        }

        return 'file';
    }

    /**
     * Return size for a given file.
     *
     * @return string
     */
    public function getFilesize()
    {
        $bytes = filesize($this->getFullPath());
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
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
