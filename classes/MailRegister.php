<?php namespace Liip\User\Classes;

use October\Rain\Mail\Mailable;
use RainLab\User\Models\User;

class MailRegister extends Mailable
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        $code = $this->user->getActivationCode();
        $data = [
            'user' => $this->user,
            'code' => $code,
            'url' => url('auth/activate/' . $code),
            'frontend_url' => env('FRONTEND_URL'),
        ];
        $this->view('liip.user::mail.register', $data);
        return $this;
    }
}