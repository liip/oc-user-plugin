<?php namespace Liip\User;

use System\Classes\PluginBase;

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
}
