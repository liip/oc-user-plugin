<?php namespace Liip\User\Classes;

use Auth;
use Event;
use Lang;
use October\Rain\Exception\ApplicationException;
use RainLab\User\Models\Settings as UserSettings;

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

    /**
     * @throws ApplicationException
     */
    public function register()
    {
        $canRegister = UserSettings::get('allow_registration', true);
        if (!$canRegister) {
            throw new ApplicationException(Lang::get('rainlab.user::lang.account.registration_disabled'));
        }
        // $requireActivation = UserSettings::get('require_activation', true);
        $automaticActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_AUTO;
        // $userActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_USER;

        $credentials = [
            'email' => request()->get('email'),
            'password' => request()->get('password'),
            'password_confirmation' => request()->get('password'),
        ];

        try {
            return Auth::register($credentials, $automaticActivation);
        } catch(\Exception $e) {
            $msg = $e->getMessage();
            return response($msg, 400);
        }

    }
}
