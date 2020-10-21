<?php namespace Liip\User\Tests\Functional;

use Auth;
use Liip\User\Classes\MailResetPassword;
use Mail;
use Liip\User\Tests\ApiTest;
use RainLab\User\Models\Settings;
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
            ->assertStatus(201)
            ->assertJson($this->user->toArray())
        ;
    }

    public function testLoginWithExitingUser()
    {
        $this->logout();
        $user = factory(User::class)->create(['password' => '12345678', 'password_confirmation' => '12345678']);

        $this->postJson('/auth/login', ['login' => $user->email, 'password' => '12345678'])
            ->assertStatus(200)
            ->assertJsonFragment(['id' => "$user->id"])
        ;
    }

    public function testLoginWithExitingUserWrongPassword()
    {
        $this->logout();
        $user = factory(User::class)->create(['password' => '12345678', 'password_confirmation' => '12345678']);

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
        $this->postJson('/auth/register', ['email' => $email, 'password' => '12345678'])
            ->assertStatus(200)
        ;

        $this->assertNotNull(User::findByEmail($email));
    }

    public function testRegisterUserThatDoesExist()
    {
        $this->postJson('/auth/register',
            [
                'email' => $this->user->email,
                'password' => '12345678'
            ])
            ->assertStatus(400)
        ;
        $this->assertCount(1, User::all());
    }

    public function testDisallowRegistration()
    {
        Settings::set('allow_registration', false);
        $this->postJson('/auth/register',
            [
                'email' => 'test@user.com',
                'password' => '12345678'
            ])
            ->assertStatus(500)
        ;
    }

    public function testRegisteredUserIsActivated()
    {
        Settings::set('allow_registration', true);

        $this->postJson('/auth/register',
            [
                'email' => 'inactive@user.com',
                'password' => '12345678'
            ])
            ->assertStatus(200)
        ;

        $user = User::findByEmail('inactive@user.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->is_activated);
    }

    public function testRestorePasswordNonExistingUser()
    {
        $this->postJson('/auth/restore-password', [
            'email' => 'no@user.com'
            ])
            ->assertStatus(200)
        ;
    }

    public function testRestorePasswordEmailIsRequired()
    {
        $this->postJson('/auth/restore-password')
            ->assertStatus(400)
            ->assertSee('Error.restore.emailInvalid')
        ;
    }

    public function testRestorePasswordSendsEmail()
    {
        Mail::fake();

        $this->postJson('/auth/restore-password', ['email' => $this->user->email])
            ->assertStatus(200)
        ;

        Mail::assertSent(MailResetPassword::class, 1);
    }
}
