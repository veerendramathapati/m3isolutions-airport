<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParameterListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parameter_lists', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('r_m_id')->comment("id from restroom master table");
                $table->unsignedBigInteger('p_m_id')->comment("id from parameter master table");
                $table->unsignedBigInteger('co_sl')->comment("id from co master table");
                $table->integer('created_by')->nullable();
                $table->dateTimeTz('created_at');
                $table->integer('updated_by')->nullable();
                $table->dateTimeTz('updated_at');
            });

            Schema::table('parameter_lists', function($table) {
                $table->foreign('p_m_id')->references('id')->on('parameter_master');
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
        Schema::dropIfExists('parameter_lists');
    }
}
