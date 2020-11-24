<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestroomMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restroom_master', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('r_name');
            $table->string('location');
            $table->string('unique_identifier');
            $table->integer('created_by')->nullable();
            $table->dateTimeTz('created_at');
            $table->integer('updated_by')->nullable();
            $table->dateTimeTz('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restroom_master');
    }
}
