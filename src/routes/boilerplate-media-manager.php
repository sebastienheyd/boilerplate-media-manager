<?php

$default = [
    'prefix'     => config('boilerplate.app.prefix', '').'/medias',
    'domain'     => config('boilerplate.app.domain', ''),
    'middleware' => ['web', 'boilerplatelocale', 'boilerplateauth', 'ability:admin,backend_access,media-manager'],
    'as'         => 'mediamanager.',
    'namespace'  => '\Sebastienheyd\BoilerplateMediaManager\Controllers',
];

Route::group($default, function () {
    Route::group(['prefix' => 'ajax', 'as' => 'ajax.'], function () {
        Route::post('list', ['as' => 'list', 'uses' => 'MediaManagerController@list']);
        Route::post('folder', ['as' => 'new-folder', 'uses' => 'MediaManagerController@newFolder']);
        Route::post('delete', ['as' => 'delete', 'uses' => 'MediaManagerController@delete']);
        Route::post('rename', ['as' => 'rename', 'uses' => 'MediaManagerController@rename']);
        Route::post('upload', ['as' => 'upload', 'uses' => 'MediaManagerController@upload']);
        Route::post('mce-upload', ['as' => 'mce-upload', 'uses' => 'MediaManagerController@uploadMce']);
    });

    Route::get('mce/{path?}', ['as' => 'mce', 'uses' => 'MediaManagerController@mce'])->where('path', '.*');
    Route::get('/{path?}', ['as' => 'index', 'uses' => 'MediaManagerController@index'])->where('path', '.*');
});
