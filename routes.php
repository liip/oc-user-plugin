<?php

Route::group(['middleware' => ['web', \Barryvdh\Cors\HandleCors::class], 'prefix' => 'auth'], function() {
    // public endpoints
    Route::post('/login', '\Liip\User\Classes\AuthController@login');
    Route::get('/logout', '\Liip\User\Classes\AuthController@logout');
    Route::post('/register', '\Liip\User\Classes\AuthController@register');
    Route::post('/activate/:code', '\Liip\User\Classes\AuthController@activate');

    // protected endpoints
    Route::group(['middleware' => \RainLab\User\Classes\AuthMiddleware::class], function() {
        Route::get('/', '\Liip\User\Classes\AuthController@index');
    });
});
