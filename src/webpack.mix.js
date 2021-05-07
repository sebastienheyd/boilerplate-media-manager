const mix = require('laravel-mix');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

mix.webpackConfig({plugins: [new CleanWebpackPlugin()]})
    .setPublicPath("public")
    .setResourceRoot('/assets/vendor/boilerplate-media-manager')
    .version();

mix.sass('resources/scss/mediamanager.scss', 'public/mediamanager.min.css');
mix.scripts('resources/js/mediamanager.js', 'public/mediamanager.min.js');

mix.sass('resources/scss/select-media.scss', 'public/select-media.min.css');
mix.scripts('resources/js/select-media.js', 'public/select-media.min.js');

mix.scripts([
    'node_modules/jquery-ui/ui/widget.js',
    'node_modules/blueimp-file-upload/js/jquery.iframe-transport.js',
    'node_modules/blueimp-file-upload/js/jquery.fileupload.js'
], 'public/vendor/blueimp-file-upload/jquery.fileupload.min.js');

mix.scripts([
    'node_modules/jquery-lazy/jquery.lazy.min.js',
    'node_modules/jquery-lazy/jquery.lazy.plugins.js'
], 'public/vendor/jquery-lazy/jquery.lazy.plugins.js');
