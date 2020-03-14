<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkAddressWorkingInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_address_working_informations', function (Blueprint $table) {
            $table->increments('id');
            $table->time('planned_start_work_time')->nullable();
            $table->time('planned_end_work_time')->nullable();
            $table->integer('candidate_number')->nullable();
            $table->text('note')->nullable();
            $table->integer('modified_manager_id')->nullable();
            $table->integer('work_address_working_day_id');
            $table->boolean('manually_modified')->nullable()->default(false);
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
        Schema::dropIfExists('work_address_working_informations');
    }
}
