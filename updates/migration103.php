<?php

namespace Liip\User\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;
use RainLab\User\Models\User;
use Str;

class Migration103 extends Migration
{
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('api_token', 80)->after('password')
                ->unique()
                ->nullable()
                ->default(null);
        });
        User::all()->each(function ($user) {
            $user->api_token = Str::random(80);
            $user->save();
        });
    }

    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('api_token');
        });
    }
}