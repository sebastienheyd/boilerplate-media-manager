<?php

use Illuminate\Support\Facades\Route;
use Sebastienheyd\BoilerplateMediaManager\Controllers\MediaManagerController;

$default = [
    'prefix' => config('boilerplate.app.prefix', '').'/medias',
    'domain' => config('boilerplate.app.domain', ''),
    'middleware' => ['web', 'boilerplatelocale', 'boilerplateauth', 'ability:admin,media_manager'],
    'as' => 'mediamanager.',
];

Route::group($default, function () {
    Route::controller(MediaManagerController::class)->group(function () {
        Route::group(['prefix' => 'ajax', 'as' => 'ajax.'], function () {
            Route::post('list', 'list')->name('list');
            Route::post('folder', 'newFolder')->name('new-folder');
            Route::post('delete', 'delete')->name('delete');
            Route::post('rename', 'rename')->name('rename');
            Route::post('upload', 'upload')->name('upload');
            Route::post('paste', 'paste')->name('paste');
        });

        Route::get('/{path?}', 'index')->name('index')->where('path', '.*');
    });
});
