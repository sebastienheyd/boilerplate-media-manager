# Laravel Boilerplate Media Manager

![Package](https://img.shields.io/badge/Package-sebastienheyd%2Fboilerplate--media--manager-lightgrey.svg)
![Laravel](https://img.shields.io/badge/Laravel-6.x-green.svg)
[![Build Status](https://scrutinizer-ci.com/g/sebastienheyd/boilerplate-media-manager/badges/build.png?b=master)](https://scrutinizer-ci.com/g/sebastienheyd/boilerplate-media-manager/build-status/master)
[![StyleCI](https://github.styleci.io/repos/170482496/shield?branch=master)](https://github.styleci.io/repos/170482496)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sebastienheyd/boilerplate-media-manager/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sebastienheyd/boilerplate-media-manager/?branch=master)
![MIT License](https://img.shields.io/github/license/sebastienheyd/boilerplate.svg)

This package will add a media management tool to [`sebastienheyd/boilerplate`](https://github.com/sebastienheyd/boilerplate)

## Installation

**Important** : Preferably, configure [`sebastienheyd/boilerplate`](https://github.com/sebastienheyd/boilerplate) before 
installing this package.

1. In order to install Laravel Boilerplate Media Manager run :

```
composer require sebastienheyd/boilerplate-media-manager
```

2. Run the command below to publish assets, lang files, ...

```
php artisan vendor:publish --provider="Sebastienheyd\BoilerplateMediaManager\ServiceProvider"
```

3. Run the migration to add permissions

```
php artisan migrate
```

4. Create the symbolic link from `public/storage` to `storage/app/public`

```
php artisan storage:link
```

## Configuration

After `vendor:publish`, you can find the configuration file `mediamanager.php` in the `app/config/boilerplate` folder

| configuration | description |
|---|---|
| mediamanager.thumbs_dir | Directory where to store dynamically generated thumbs |
| mediamanager.authorized.size | Upload max size in bytes, default is 2048 |
| mediamanager.authorized.mimes | Mime types by extension, see [Laravel documentation](https://laravel.com/docs/5.7/validation#rule-mimes)
| mediamanager.filetypes | Associative array to get file type by extension |
| mediamanager.icons | Associative array to get icon class (Fontawesome) by file type |
| mediamanager.filter | Array of filtered files to hide |

## Integration

### Img (fit or resize)

Img will dynamically resize an image and returns the URL using Intervention and Storage (public disk)

```blade
{!! img('/storage/my_picture.jpg', 100, 100, [], 'resize') !!}
```

will return

```blade
<img src="/storage/thumbs/actualites/resize/100x100/my_picture.jpg?1555331285" width="100" height="100">
```

Or using the Blade directive :

```blade
@img('/storage/my_picture.jpg', 250, 150, ['class' => 'fluid-image'])
```

will return

```blade
<img src="/storage/thumbs/actualites/fit/250x150/my_picture.png?1555331285" width="250" height="150" class="fluid-image">
```

You can already get only the url by using `img_url` helper function.

#### Clear cache

You can clear all resized image by using the artisan command `thumbs:clear`

```
php artisan thumbs:clear
```

### TinyMCE

TinyMCE is supported by using the "load" view included in this package :

```blade
@include('boilerplate-media-manager::load.tinymce')

<script>
    $('#content').tinymce({})
</script>
```

## Package update

Laravel Boilerplate Media Manager comes with assets such as Javascript, CSS, and images. Since you typically will need to overwrite the assets
every time the package is updated, you may use the ```--force``` flag :

```
php artisan vendor:publish --provider="Sebastienheyd\BoilerplateMediaManager\ServiceProvider" --tag=public --force
```

To auto update assets each time package is updated, you can add this command to `post-autoload-dump` into the 
file `composer.json` at the root of your project.
 

```json
{
    "scripts": {
        "post-autoload-dump": [
            "@php artisan vendor:publish --provider=\"Sebastienheyd\\BoilerplateMediaManager\\BoilerplateMediaManagerServiceProvider\" --tag=public --force -q",
        ]
    }
}
```
