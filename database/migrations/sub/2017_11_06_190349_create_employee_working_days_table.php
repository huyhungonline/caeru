<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeWorkingDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_working_days', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id');
            $table->date('date');
            $table->boolean('concluded_level_one')->default(false);
            $table->boolean('concluded_level_two')->default(false);
            $table->integer('exchanged_schedule_day_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['employee_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_working_days');
    }
}
