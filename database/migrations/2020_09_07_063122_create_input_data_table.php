<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInputDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('input_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('description')->nullable();
            $table->string('image');
            $table->unsignedBigInteger('c_l_id')->comment("checklists id from checklists table	");
            $table->unsignedBigInteger('co_sl')->comment("id from co master table");
            $table->integer('created_by')->nullable();
            $table->dateTimeTz('created_at');
            $table->integer('updated_by')->nullable();
            $table->dateTimeTz('updated_at');
        });
        Schema::table('input_data', function($table) {
            $table->foreign('c_l_id')->references('id')->on('checklists');
            $table->foreign('co_sl')->references('id')->on('co_master');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('input_data');
    }
}
