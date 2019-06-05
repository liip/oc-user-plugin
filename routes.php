<?php

Route::group(['middleware' => ['web', \Barryvdh\Cors\HandleCors::class], 'prefix' => 'auth'], function() {
    // public endpoints
    Route::post('/login', '\Liip\User\Classes\AuthController@login');
    Route::get('/logout', '\Liip\User\Classes\AuthController@logout');

    // protected endpoints
    Route::group(['middleware' => \RainLab\User\Classes\AuthMiddleware::class], function() {
        Route::get('/', '\Liip\User\Classes\AuthController@index');
    });
});
