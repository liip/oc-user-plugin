<?php namespace Liip\User;

use Liip\User\Classes\AuthManager;
use System\Classes\PluginBase;
use App;

/**
 * user Plugin Information File
 */
class Plugin extends PluginBase
{
    public $require = [
        'RainLab.User',
        'Liip.Cors',
    ];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'User',
            'description' => 'expose rainlab user as rest api',
            'author'      => 'liip',
            'icon'        => 'icon-leaf'
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'liip.user::mail.register',
            'liip.user::mail.restore'
        ];
    }

    public function register()
    {
        parent::register();
        App::singleton('user.auth', function() {
            return AuthManager::instance();
        });
    }
}
