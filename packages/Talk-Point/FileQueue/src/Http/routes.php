<?php


Route::group(['prefix' => 'api', 'namespace' => 'TPTaskRunner\Http\Controllers'], function () {
    Route::get('v1/tasks/run/{id}', 'TaskAPIController@run')->name('api.v1.tasks.run');
    Route::resource('v1/tasks', 'TaskAPIController');
});