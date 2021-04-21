<?php

Route::group(['middleware' => ['web'], 'prefix' => 'auth'], function () {
    // public endpoints
    Route::post('/login', '\Liip\User\Classes\AuthController@login');
    Route::get('/logout', '\Liip\User\Classes\AuthController@logout');
    Route::post('/register', '\Liip\User\Classes\AuthController@register');
    Route::get('/activate/{code}', '\Liip\User\Classes\AuthController@activate');
    Route::post('/restore-password', '\Liip\User\Classes\AuthController@restorePassword');
    Route::post('/set-password', '\Liip\User\Classes\AuthController@setPassword');

    // protected endpoints
    Route::group(['middleware' => [\Liip\User\Classes\TokenMiddleware::class, \RainLab\User\Classes\AuthMiddleware::class]], function () {
        Route::get('/', '\Liip\User\Classes\AuthController@index');
    });
});
