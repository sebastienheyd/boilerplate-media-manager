# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Laravel "Media Manager" package for [sebastienheyd/boilerplate](https://github.com/sebastienheyd/boilerplate). Provides a file/image manager integrated into the boilerplate admin panel. Supports Laravel 6.x to 12.x.

Composer package: `sebastienheyd/boilerplate-media-manager`

## Conventions

Commit messages, code comments, and PR/MR descriptions must be written in English.

## Development commands

```bash
# Tests (stop on first failure)
make test

# Tests with code coverage
make testcoverage

# Check coding standards (PSR-2)
make cs

# Auto-fix coding standards
make csfix

# Build frontend assets (from src/)
cd src && npm run dev
cd src && npm run production
```

Run a specific test:
```bash
vendor/bin/phpunit --filter=TestName
```

## Architecture

### Package structure

All source code lives in `src/`. Root namespace is `Sebastienheyd\BoilerplateMediaManager`.

- `ServiceProvider.php`: package entry point, registers routes, views, translations, migrations, config, Blade directives, and the Intervention Image service
- `Controllers/MediaManagerController.php`: main controller, handles all AJAX operations (list, upload, delete, rename, new folder, paste)
- `Models/`: business classes `Path`, `Directory`, `File`, `Breadcrumb` for filesystem management
- `Lib/ImageResizer.php`: dynamic image resizing (modes `fit` and `resize`), stores results in a thumbs directory
- `View/Composers/`: view composers for the `image` and `file` Blade components
- `Menu/BoilerplateMediaManager.php`: menu registration in boilerplate
- `Commands/Clearthumbs.php`: artisan command `thumbs:clear`
- `helpers.php`: global functions `img()` and `img_url()` (auto-loaded via composer)

### Routes

Prefix `/medias`, boilerplate middleware (auth + `media_manager` permission). All operations go through AJAX POST calls under `/medias/ajax/*`.

### Frontend assets

JS/SCSS sources in `src/resources/`, compiled via Webpack Mix (`src/webpack.mix.js`), minified output in `src/public/`. Uses jQuery, jQuery UI, Blueimp File Upload.

### Config

File `src/config/mediamanager.php`, merged under the `boilerplate.mediamanager` key. Main settings: max upload size, allowed MIME types, thumbs directory.

### Tests

Uses Orchestra Testbench. The base `TestCase` loads `BoilerplateServiceProvider` and the package `ServiceProvider`. Tests live in `tests/`, mainly Blade component tests.

### CI

GitHub Actions: tests on PHP 8.2 with Laravel 11 and 12. Runs phpcs then phpunit.
