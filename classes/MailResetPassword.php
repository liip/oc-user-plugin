<?php namespace Liip\User\Classes;

use October\Rain\Mail\Mailable;
use RainLab\User\Models\User;

class MailResetPassword extends Mailable
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        $code = $this->user->getResetPasswordCode();
        $data = [
            'user' => $this->user,
            'code' => $code,
            'url' => env('FRONTEND_URL') . '/password?code=' . $code,
        ];
        $this->view('liip.user::mail.restore', $data);

        return $this;
    }
}
