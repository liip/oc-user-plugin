<?php namespace Liip\User\Tests;

use Illuminate\Database\Eloquent\Factory;
use Faker\Generator as Faker;
use RainLab\User\Models\User;

class Factories
{
    public static function register()
    {
        $factory = app(Factory::class);

        $factory->define(User::class, function (Faker $faker) {
            $password = $faker->password(8, 255);
            return [
                'username' => $faker->userName,
                'email' => $faker->email,
                'password' => $password,
                'password_confirmation' => $password,
                'is_activated' => true,
            ];
        });
    }
}
