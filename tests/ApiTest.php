<?php namespace Liip\User\Tests;

use PluginTestCase;
use RainLab\User\Models\User;
use Auth;

class ApiTest extends PluginTestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();
        Factories::register();

        $this->user = factory(User::class)->create();
        $this->impersonate($this->user);
    }

    public function tearDown()
    {
        Auth::logout();
        parent::tearDown();
    }

    protected function impersonate($user)
    {
        Auth::impersonate($this->user);
        return $this;
    }

    protected function logout()
    {
        Auth::logout();
        return $this;
    }
}