<?php namespace Liip\User\Tests;

use Auth;
use RainLab\User\Models\User;

class UserApiTest extends ApiTest
{
    public function testCantAccessAuthEndpointNotLoggedIn()
    {
        $this->logout();

        $this->get('/auth')
            ->assertStatus(403)
            ;
    }

    public function testCanAccessAuthEndpointLoggedIn()
    {
        $this->get('/auth')
            ->assertStatus(200)
            ->assertJson($this->user->toArray())
        ;
    }

    public function testLoginWithExitingUser()
    {
        $this->logout();
        $user = factory(User::class)->create(['password' => '1234', 'password_confirmation' => '1234']);

        $this->postJson('/auth/login', ['login' => $user->email, 'password' => '1234'])
            ->assertStatus(200)
            ->assertJsonFragment(['id' => "$user->id"])
        ;
    }

    public function testLoginWithExitingUserWrongPassword()
    {
        $this->logout();
        $user = factory(User::class)->create(['password' => '1234', 'password_confirmation' => '1234']);

        $this->postJson('/auth/login', ['login' => $user->email, 'password' => '123'])
            ->assertStatus(403)
        ;
    }

    public function testLoginWithNonExitingUser()
    {
        $this->logout();
        $this->postJson('/auth/login', ['login' => 'idontexists', 'password' => 'xyz1234'])
            ->assertStatus(403)
        ;
    }

    public function testRegisterUserThatDoesntExist()
    {
        $email = 'test@user.com';
        $this->postJson('/auth/register', ['email' => $email, 'password' => '1234'])
            ->assertStatus(200)
        ;
        $this->assertNotNull(User::findByEmail($email));
    }

    public function testRegisterUserThatDoesExist()
    {
        $this->postJson('/auth/register',
            [
                'email' => $this->user->email,
                'password' => '1234'
            ])
            ->assertStatus(500)
        ;
        $this->assertCount(1, User::all());
    }
}
