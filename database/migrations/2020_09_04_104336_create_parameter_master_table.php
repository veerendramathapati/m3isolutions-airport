<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParameterMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parameter_master', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('p_name')->comment("parameter name");
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
        Schema::dropIfExists('parameter_master');
    }
}
