<?php namespace Liip\User\Classes;

use \RainLab\User\Classes\AuthManager as BaseAuthManager;

class AuthManager extends BaseAuthManager
{
    public function extendUserQuery($query)
    {
        parent::extendUserQuery($query);
    }
}