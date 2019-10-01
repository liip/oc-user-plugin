<?php namespace Liip\User\Updates;

use October\Rain\Database\Schema\Blueprint;
use Schema;
use October\Rain\Database\Updates\Migration;

class Migration102 extends Migration
{
    public function up()
    {
        Schema::create('liip_user_roles', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->unique('liip_user_role_unique');
            $table->string('code')->nullable()->index('liip_user_role_code_index');
            $table->text('description')->nullable();
            $table->text('permissions')->nullable();
            $table->timestamps();
        });
        Schema::table('users', function(Blueprint $table) {
            $table->integer('role_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('role_id');
        });
        Schema::drop('liip_user_roles');
    }
}