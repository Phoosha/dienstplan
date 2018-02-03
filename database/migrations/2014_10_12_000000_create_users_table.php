<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name', 35);
            $table->string('last_name', 35);
            $table->string('login', 35)->unique();
            $table->string('email', 100);
            $table->string('phone', 35)->default('');
            $table->string('password');
            $table->boolean('is_admin')->default(false);
            $table->timestamp('last_training')->nullable();
            $table->string('api_token')->unique()->nullable();
            $table->string('register_token')->unique()->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
