<?php

namespace Liip\User\Classes;

use Closure;
use RainLab\User\Facades\Auth;
use RainLab\User\Models\User;
use Event;

class TokenMiddleware
{
    public function handle($request, Closure $next)
    {
        $token = $request->query('token');
        if (!empty($token)) {
            $user = User::where('api_token', $token)->first();
            if ($user != null) {
                if ($message = Event::fire('liip.user.authenticated', [$user], true)) {
                    return response($message, 403);
                }
                Auth::setUser($user);
            }
        }
        return $next($request);
    }
}
