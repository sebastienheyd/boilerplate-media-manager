# Laravel Media Manager for [sebastienheyd/boilerplate](https://github.com/sebastienheyd/boilerplate)

[![Packagist](https://img.shields.io/packagist/v/sebastienheyd/boilerplate-media-manager.svg?style=flat-square)](https://packagist.org/packages/sebastienheyd/boilerplate-media-manager)
[![StyleCI](https://github.styleci.io/repos/170482496/shield?branch=master)](https://github.styleci.io/repos/170482496)
![Laravel](https://img.shields.io/badge/Laravel-6.x%20→%2012.x-green?logo=Laravel&style=flat-square)
[![Nb downloads](https://img.shields.io/packagist/dt/sebastienheyd/boilerplate-media-manager.svg?style=flat-square)](https://packagist.org/packages/sebastienheyd/boilerplate-media-manager)
[![MIT License](https://img.shields.io/github/license/sebastienheyd/boilerplate-media-manager.svg?style=flat-square)](license.md)

This package adds a media management tool to [`sebastienheyd/boilerplate`](https://github.com/sebastienheyd/boilerplate)

## Installation

1. In order to install Laravel Boilerplate Media Manager run :

```
composer require sebastienheyd/boilerplate-media-manager
```

2. Run the migration to add permissions

```
php artisan migrate
```

3. Create the symbolic link from `public/storage` to `storage/app/public`

```
php artisan storage:link
```

**Optional**: 

To publish configuration files, you can run:

```
php artisan vendor:publish --tag=boilerplate
```

## Configuration

After `vendor:publish`, you can find the configuration file `mediamanager.php` in the `app/config/boilerplate` folder

| configuration                   | description                                                                                              |
|---------------------------------|----------------------------------------------------------------------------------------------------------|
| mediamanager.base_url           | Relative path to the public storage folder                                                               |
| mediamanager.tinymce_upload_dir | Directory where TinyMCE will store his edited image                                                      |
| mediamanager.thumbs_dir         | Directory where to store dynamically generated thumbs                                                    |
| mediamanager.authorized.size    | Upload max size in bytes, default is 2048                                                                |
| mediamanager.authorized.mimes   | Mime types by extension, see [Laravel documentation](https://laravel.com/docs/5.7/validation#rule-mimes) |
| mediamanager.filetypes          | Associative array to get file type by extension                                                          |
| mediamanager.icons              | Associative array to get icon class (Fontawesome) by file type                                           |
| mediamanager.filter             | Array of filtered files to hide                                                                          |

## Backend

### TinyMCE

This media manager will be automatically used for images and files inclusion by the [TinyMCE Blade component](https://sebastienheyd.github.io/boilerplate/docs/8.x/components/tinymce.html) included with the [sebastienheyd/boilerplate](https://github.com/sebastienheyd/boilerplate) package.

### Image selector

You can use the `<x-boilerplate-media-manager::image>` component to easily insert an image selector into your forms. 
This component allows you to use the media manager to select an image to use.

```html
<x-boilerplate-media-manager::image name="image">
```

Parameters are :

| name        | description                    | default |
|-------------|--------------------------------|---------|
| name        | Input name (required)          | ""      |
| value       | Default input value            | ""      |
| label       | Label of the input field       | ""      |
| width       | Width of the selector          | 300     |
| height      | Height of the selector         | 200     |
| help        | Help text                      | ""      |
| group-class | Additional class to form-group | ""      |
| group-id    | Form-group ID                  | ""      |

### File selector

You can use the `<x-boilerplate-media-manager::file>` component to easily insert a file selector into your forms. 
This component allows you to use the media manager to select a file to assign to the input field.

```html
<x-boilerplate-media-manager::file name="file">
```

Parameters are :

| name        | description                                 | default |
|-------------|---------------------------------------------|---------|
| name        | Input name (required)                       | ""      |
| value       | Input value                                 | ""      |
| label       | Label of the input field                    | ""      |
| type        | Media list filter (all, file, image, video) | all     |
| help        | Help text                                   | ""      |
| group-class | Additional class to form-group              | ""      |
| group-id    | Form-group ID                               | ""      |

## Frontend

### Img (fit or resize)

`img` will dynamically resize an image and returns the URL using Intervention and Storage (public disk)

```blade
{!! img('/storage/my_picture.jpg', 100, 100, [], 'resize') !!}
```

will return

```html
<img src="/storage/thumbs/resize/100x100/my_picture.jpg?1555331285" width="100" height="100">
```

Or using the `@img` Blade directive :

```blade
@img('/storage/my_picture.jpg', 250, 150, ['class' => 'fluid-image'])
```

will return

```html
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
