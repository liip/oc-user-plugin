<?php namespace Liip\User\Classes;

use Auth;
use Event;
use Lang;
use Mail;
use Log;
use RainLab\User\Models\User as UserModel;
use Validator;
use October\Rain\Exception\ApplicationException;
use RainLab\User\Models\Settings as UserSettings;
use RainLab\User\Models\User;

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
            $user = Auth::authenticate(request()->all());
            if ($message = Event::fire('liip.user.authenticated', [$user], true)) {
                Auth::logout();
                return response( $message, 403);
            } else {
                return $user;
            }
        } catch (\October\Rain\Auth\AuthException $e) {
            $code = $e->getCode();
            $errorMessage = 'Error.server';
                        
            if ($code === AuthManager::ERROR_ACTIVATED || $code === 0) {
                $errorMessage = 'Error.activated';
            }
            if ($code === AuthManager::ERROR_BANNED) {
                $errorMessage = 'Error.banned';
            }
            if ($code === AuthManager::ERROR_AUTHENTICATION) {
                $errorMessage = 'Error.authentication';
            }
            if ($code === AuthManager::ERROR_SUSPENDED) {
                $errorMessage = 'Error.suspended';
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

        $password = request()->get('password');
        $credentials = [
            'email' => request()->get('email'),
            'password' => $password,
            'password_confirmation' => $password,
        ];
        try {
            $user = Auth::register($credentials);
            Mail::to($user)->send(new MailRegister($user));
        } catch(\Exception $e) {
            $msg = $e->getMessage();
            return response($msg, 400);
        }
    }

    public function activate($code)
    {
        $user = User::where('activation_code', $code)->first();
        if($user === null) {
            return response('Error.activate.codeInvalid', 400);
        }
        if (!$user->attemptActivation($code)) {
            return response('Error.activate.attemptFailed', 400);
        }

        return redirect(env('FRONTEND_URL'));
    }

    public function restorePassword()
    {
        $rules = [
            'email' => 'required|email|between:6,255'
        ];        
        $validation = Validator::make(request()->all(), $rules);
        if ($validation->fails()) {
            return response('Error.restore.emailInvalid', 400);
        }

        $user = UserModel::findByEmail(request()->post('email'));
        if( $user ) {
            Mail::to($user)->send(new MailResetPassword($user));
        }
    }

    public function setPassword()
    {
        $rules = [
            'password' => 'required|between:8,255',
            'code' => 'required',
        ];
        $validation = Validator::make(request()->all(), $rules);
        if($validation->fails()) {
            return response('Error.restore_validation', 400);
        }

        $user = User::where('reset_password_code', request()->post('code'))->first();

        if (!$user || !$user->attemptResetPassword(request()->post('code'), request()->post('password'))) {
            return response('Error.restore_code_invalid', 400);
        }
    }
}
