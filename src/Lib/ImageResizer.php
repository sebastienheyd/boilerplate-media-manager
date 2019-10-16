<?php

namespace Sebastienheyd\BoilerplateMediaManager\Lib;

use Image;
use Storage;

class ImageResizer
{
    protected $path;
    protected $pathinfo;
    protected $width;
    protected $height;
    protected $type;
    protected $original_file;
    protected $dest_file;
    protected $storage;

    /**
     * Get resized image url from the given public path
     *
     * @param string $path Image public path (/storage/*)
     * @param integer $width
     * @param integer $height
     * @param string $type
     *
     * @return string
     */
    public static function url(string $path, $width, $height, $type = 'fit')
    {
        return (new self($path))->setSize($width, $height, $type)->getUrl();
    }

    /**
     * ImageResizer constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = preg_replace('#^/storage/#', '', $path);
        $this->pathinfo = pathinfo($this->path);
        $this->storage = Storage::disk('public');
    }

    /**
     * Set thumbnail size
     *
     * @param integer $width
     * @param integer $height
     * @param string $type
     *
     * @return $this
     */
    public function setSize($width, $height, $type = 'fit')
    {
        $this->width = $width;
        $this->height = $height;
        $this->type = $type;

        $this->original_file = $this->storage->path($this->path);

        $destFile = config('mediamanager.thumbs_dir', 'thumbs').'/';
        $destFile .= $this->pathinfo['dirname'].'/'.$type.'/'.$width.'x'.$height.'/';
        $destFile .= $this->pathinfo['basename'];

        $this->dest_file = $destFile;

        return $this;
    }

    /**
     * Get thumbnail size
     *
     * @return string
     */
    public function getUrl()
    {
        if (!$this->storage->exists($this->path)) {
            return '';
        }

        if (!in_array(strtolower($this->pathinfo['extension']), ['jpg', 'jpeg', 'png', 'gif'])) {
            return '';
        }

        if ($this->storage->exists($this->dest_file)) {
            return $this->storage->url($this->dest_file).'?'.filemtime($this->original_file);
        }

        switch ($this->type) {
            case 'fit':
            case 'resize':
                try {
                    $mime = $this->storage->mimeType($this->path);

                    $image = Image::make($this->original_file)
                        ->{$this->type}($this->width, $this->height, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })->encode($mime);

                    $this->storage->put($this->dest_file, (string) $image);
                    return $this->storage->url($this->dest_file).'?'.filemtime($this->original_file);
                } catch (\Exception $e) {
                    return '';
                }
                break;
            default:
                return '';
        }
    }
}
