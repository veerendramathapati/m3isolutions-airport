<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_master', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('c_name')->comment("check name	");
            $table->unsignedBigInteger('type_id')->comment("type id from type master table	");
            $table->unsignedBigInteger('co_sl')->comment("id from co master table");
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
        Schema::dropIfExists('check_master');
    }
}
