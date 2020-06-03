<?php

if (!function_exists('img')) {
    function img($path, $width, $height, $options = [], $type = 'fit')
    {
        $path = preg_replace('`\?.*?$`', '', $path);

        if (empty($path)) {
            return '';
        }

        $url = url($path);
        if (pathinfo($path, PATHINFO_EXTENSION) !== 'svg') {
            $img = new \Sebastienheyd\BoilerplateMediaManager\Lib\ImageResizer($path);
            $img->setSize($width, $height, $type);
            $url = $img->getUrl();

            if ($type === 'resize') {
                [$width, $height] = $img->getDestFileSize();
            }
        }

        if ($url === '') {
            return '';
        }

        $opts = '';
        foreach ($options as $k => $v) {
            $opts .= $k.'="'.$v.'" ';
        }

        return sprintf('<img src="%s" width="%s" height="%s" %s>', $url, $width, $height, $opts);
    }
}

if (!function_exists('img_url')) {
    function img_url($path, $width, $height, $type = 'fit')
    {
        $path = preg_replace('`\?.*?$`', '', $path);

        if (empty($path)) {
            return '';
        }

        if (pathinfo($path, PATHINFO_EXTENSION) === 'svg') {
            return url($path);
        }

        return \Sebastienheyd\BoilerplateMediaManager\Lib\ImageResizer::url($path, $width, $height, $type);
    }
}
