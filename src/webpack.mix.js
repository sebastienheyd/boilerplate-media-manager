let mix = require('laravel-mix');
const Clean = require('clean-webpack-plugin');

mix.webpackConfig({plugins: [new Clean(['public'], {verbose: false})]})
    .setPublicPath("public")
    .setResourceRoot('/');

mix.sass('resources/scss/mediamanager.scss', 'public/mediamanager.min.css').version();

mix.scripts('resources/js/mediamanager.js', 'public/mediamanager.min.js').version();

mix.scripts([
    'node_modules/jquery-ui/ui/widget.js',
    'node_modules/blueimp-file-upload/js/jquery.iframe-transport.js',
    'node_modules/blueimp-file-upload/js/jquery.fileupload.js'
], 'public/vendor/blueimp-file-upload/jquery.fileupload.min.js').version();

mix.scripts([
    'node_modules/jquery-lazy/jquery.lazy.min.js',
    'node_modules/jquery-lazy/jquery.lazy.plugins.js'
], 'public/vendor/jquery-lazy/jquery.lazy.plugins.js').version();
