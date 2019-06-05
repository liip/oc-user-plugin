<?php namespace Liip\User\Classes;

use Auth;
use Event;

/**
 * Class AuthController
 */
class AuthController
{
    public function index()
    {
        return Auth::getUser();
    }

    public function login()
    {
        try {
            $user = Auth::authenticate(request()->json()->all());
            if ($message = Event::fire('liip.user.authenticated', [$user], true)) {
                Auth::logout();
                throw new \October\Rain\Auth\AuthException($message);
            } else {
                return $user;
            }
        } catch (\October\Rain\Auth\AuthException $e) {
            $errorMessage = 'Error.server';
            $msg = $e->getMessage();

            if (strpos($msg, 'banned') !== FALSE) {
                $errorMessage = 'Error.banned';
            }
            if (strpos($msg, 'activated') !== FALSE) {
                $errorMessage = 'Error.activated';
            }
            if (strpos($msg, 'password') !== FALSE || strpos($msg, 'credentials') !== FALSE) {
                $errorMessage = 'Error.authentication';
            }
            if (strpos($msg, 'suspended') !== FALSE) {
                $errorMessage = 'Error.suspended';
            }
            if (strpos($msg, 'Error.customerInactive') !== FALSE) {
                $errorMessage = 'Error.customerInactive';
            }
            return response($errorMessage, 403);
        }
    }

    public function logout()
    {
        Auth::logout();
    }

    public function register()
    {
        $credentials = [
            'email' => request()->get('email'),
            'password' => request()->get('password'),
            'password_confirmation' => request()->get('password'),
        ];
        Auth::register($credentials);
    }
}
