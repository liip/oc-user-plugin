<?php namespace Liip\User\Classes;

use Closure;
use Auth;

class CheckPermission
{
    public function handle($request, Closure $next, $permission) {
        $user = Auth::getUser();
        if ($user === null) {
            return response('not authenticated', 401);
        }
        if (!$user->hasUserPermission($permission)) {
            return response('not authorized', 403);
        }
        return $next($request);
    }

    public static function has($permission)
    {
        return self::class . ':' . $permission;
    }
}
