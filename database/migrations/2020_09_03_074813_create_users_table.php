<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->bigIncrements('id');
            $table->string('username');
            $table->bigInteger('role_id')->unsigned();
            $table->Integer('login_id')->unsigned();
            $table->string('password');
            $table->string('email')->unique();
            $table->Integer('mobile_no')->unique();
            $table->string('status')->comment("active=1","inactive=0");
            $table->integer('created_by')->nullable();
            $table->dateTimeTz('created_at');
            $table->integer('updated_by')->nullable();
            $table->dateTimeTz('updated_at');
        });
        Schema::table('users', function($table) {
            $table->foreign('role_id')->references('id')->on('role_master');

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
