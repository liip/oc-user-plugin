<?php


Route::group(['middleware' => ['web', \Barryvdh\Cors\HandleCors::class], 'prefix' => 'auth'], function() {
    Route::post('/login', function() {
        try {
            $user = Auth::authenticate(request()->json()->all());
            if($message = Event::fire('liip.user.authenticated', [$user], true)) {
                Auth::logout();
                return response($message, 403);
            } else {
                return $user;
            }
        } catch(\October\Rain\Auth\AuthException $e) {
            return response('Username or Password wrong', 422);
        }
    });

    Route::get('/logout', function() {
        return Auth::logout();
    });

    Route::group(['middleware' => \RainLab\User\Classes\AuthMiddleware::class], function() {
        Route::get('/', function() {
            return Auth::getUser();
        });
    });
});
