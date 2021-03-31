# Laravel Media Manager for [sebastienheyd/boilerplate](https://github.com/sebastienheyd/boilerplate)

[![Packagist](https://img.shields.io/packagist/v/sebastienheyd/boilerplate-media-manager.svg?style=flat-square)](https://packagist.org/packages/sebastienheyd/boilerplate-media-manager)
[![Build Status](https://scrutinizer-ci.com/g/sebastienheyd/boilerplate-media-manager/badges/build.png?b=master)](https://scrutinizer-ci.com/g/sebastienheyd/boilerplate-media-manager/build-status/master)
[![StyleCI](https://github.styleci.io/repos/170482496/shield?branch=master)](https://github.styleci.io/repos/170482496)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sebastienheyd/boilerplate-media-manager/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sebastienheyd/boilerplate-media-manager/?branch=master)
![Laravel](https://img.shields.io/badge/Laravel-6.x%20→%208.x-green?logo=Laravel&style=flat-square)
[![Nb downloads](https://img.shields.io/packagist/dt/sebastienheyd/boilerplate-media-manager.svg?style=flat-square)](https://packagist.org/packages/sebastienheyd/boilerplate-media-manager)
[![MIT License](https://img.shields.io/github/license/sebastienheyd/boilerplate-media-manager.svg?style=flat-square)](license.md)

This package adds a media management tool to [`sebastienheyd/boilerplate`](https://github.com/sebastienheyd/boilerplate)

## Installation

1. In order to install Laravel Boilerplate Media Manager run :

```
composer require sebastienheyd/boilerplate-media-manager
```

2. Run the command below to publish assets and configuration file

```
php artisan vendor:publish --tag=boilerplate
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
| mediamanager.base_url | Relative path to the public storage folder  |
| mediamanager.tinymce_upload_dir | Directory where TinyMCE will store his edited image  |
| mediamanager.thumbs_dir | Directory where to store dynamically generated thumbs |
| mediamanager.authorized.size | Upload max size in bytes, default is 2048 |
| mediamanager.authorized.mimes | Mime types by extension, see [Laravel documentation](https://laravel.com/docs/5.7/validation#rule-mimes)
| mediamanager.filetypes | Associative array to get file type by extension |
| mediamanager.icons | Associative array to get icon class (Fontawesome) by file type |
| mediamanager.filter | Array of filtered files to hide |

## Backend

### TinyMCE

TinyMCE is supported by using the "load" view included in this package :

```blade
@include('boilerplate-media-manager::load.tinymce')

<script>
    $('#content').tinymce({})
</script>
```

### Image selector

You can use the `boilerplate-media-manager::select.image` view to insert an input field into your forms. 
This view allows you to use the media manager to select the image to use.

```blade
@include('boilerplate-media-manager::select.image', ['name' => 'myImageInputName', 'value' => '/storage/picture.jpg'])
```

Parameters are :

| name | description | default |
|---|---|---|
| name | Input name | image |
| value | Input value | "" |
| width | Width of the selector | 300 |
| height | Height of the selector | 200 |

### File selector

You can use the `boilerplate-media-manager::select.file` view to insert an input field into your forms. 
This view allows you to use the media manager to select the file to assign to the input field.

```blade
@include('boilerplate-media-manager::select.file', ['label' => 'My file', 'name' => 'file', 'value' => $myFile, 'type' => 'file'])
```

Parameters are :

| name | description | default |
|---|---|---|
| label | Label of the input field | "" |
| name |  Input name | file |
| value | Input value | "" |
| type | Media list filter (all, file, image, video) | all |

## Frontend

### Img (fit or resize)

`img` will dynamically resize an image and returns the URL using Intervention and Storage (public disk)

```blade
{!! img('/storage/my_picture.jpg', 100, 100, [], 'resize') !!}
```

will return

```blade
<img src="/storage/thumbs/resize/100x100/my_picture.jpg?1555331285" width="100" height="100">
```

Or using the `@img` Blade directive :

```blade
@img('/storage/my_picture.jpg', 250, 150, ['class' => 'fluid-image'])
```

will return

```blade
<img src="/storage/thumbs/fit/250x150/my_picture.png?1555331285" width="250" height="150" class="fluid-image">
```

You can already get only the url by using `img_url` helper function.

#### Clear cache

You can clear all resized image by using the artisan command `thumbs:clear`

```
php artisan thumbs:clear
```

## Language

You can translate or change translations by running `php artisan vendor:publish --tag=boilerplate-media-manager-lang`.
After running this command, you will find translations folders into `resources/lang/vendor/boilerplate-media-manager`.
Copy one of the language folders in the new language you want to create and all you have to do is to translate. If you
want to share the language you have added, don't hesitate to make a pull request.

### Views

You can override views by running `php artisan vendor:publish --tag=boilerplate-media-manager-views`. You will then find
the views in the `resources/views/vendor/boilerplate-media-manager` folder.

## Package update

Laravel Boilerplate Media Manager comes with assets such as Javascript, CSS, and images. Since you typically will need to overwrite the assets
every time the package is updated, you may use the ```--force``` flag :

```
php artisan vendor:publish --tag=boilerplate-public --force
```

To auto update assets each time package is updated, you can add this command to `post-autoload-dump` into the 
file `composer.json` at the root of your project.


```json
{
    "scripts": {
        "post-autoload-dump": [
            "@php artisan vendor:publish --tag=boilerplate-public --force -q",
        ]
    }
}
```
