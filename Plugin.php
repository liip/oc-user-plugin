<?php namespace Liip\User;

use System\Classes\PluginBase;

/**
 * user Plugin Information File
 */
class Plugin extends PluginBase
{
    public $require = [
        'rainlab.user',
        'liip.cors',
    ];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'user',
            'description' => 'expose rainlab user as rest api',
            'author'      => 'liip',
            'icon'        => 'icon-leaf'
        ];
    }
}
