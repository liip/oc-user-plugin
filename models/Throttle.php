<?php namespace Liip\User\Models;

use Liip\User\Classes\AuthManager;
use October\Rain\Auth\AuthException;
use October\Rain\Auth\Models\Throttle as BaseThrottle;

class Throttle extends BaseThrottle
{
    protected $table = 'user_throttle';

    public function check()
    {
        if ($this->is_banned) {
            throw new AuthException(sprintf(
                'User [%s] has been banned.', $this->user->getLogin()
            ), AuthManager::ERROR_BANNED);
        }

        if ($this->checkSuspended()) {
            throw new AuthException(sprintf(
                'User [%s] has been suspended.', $this->user->getLogin()
            ), AuthManager::ERROR_SUSPENDED);
        }

        return true;
    }
}
