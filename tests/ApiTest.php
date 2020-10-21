<?php namespace Liip\User\Tests;

use PluginTestCase;
use RainLab\User\Models\Settings;
use RainLab\User\Models\User;
use System\Classes\MailManager;
use System\Classes\PluginManager;
use Auth;
use App;

class ApiTest extends PluginTestCase
{
    /**
     * @var array   Plugins to refresh between tests.
     */
    protected $refreshPlugins = [
        'RainLab.User',
    ];

    protected $user;

    public function setUp() : void
    {
        parent::setUp();

        // Get the plugin manager
        $pluginManager = PluginManager::instance();
        // Register the plugins to make features like file configuration available
        $pluginManager->registerAll(true);
        // Boot all the plugins to test with dependencies of this plugin
        $pluginManager->bootAll(true);

        Settings::resetDefault();

        // we register templates so load them
        MailManager::instance()->loadRegisteredTemplates();

        Factories::register();
        $this->user = factory(User::class)->create();
        $this->impersonate($this->user);
    }

    public function tearDown() : void
    {
        Auth::logout();
        // Get the plugin manager
        $pluginManager = PluginManager::instance();

        // Ensure that plugins are registered again for the next test
        $pluginManager->unregisterAll();

        parent::tearDown();
    }

    protected function impersonate($user)
    {
        Auth::impersonate($user);
        return $this;
    }

    protected function logout()
    {
        Auth::logout();
        return $this;
    }
}
