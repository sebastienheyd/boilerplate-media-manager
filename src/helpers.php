<?php

if (!function_exists('img')) {
    function img($path, $width, $height, $options = [], $type = 'fit')
    {
        $url = img_url($path, $width, $height, $type);

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
        return \Sebastienheyd\BoilerplateMediaManager\Lib\ImageResizer::url($path, $width, $height, $type);
    }
}