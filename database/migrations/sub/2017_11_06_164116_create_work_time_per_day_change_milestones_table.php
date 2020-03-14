<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkTimePerDayChangeMilestonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_time_per_day_change_milestones', function (Blueprint $table) {
            $table->increments('id');
            $table->date('change_date');
            $table->time('new_work_time_per_day');
            $table->time('old_work_time_per_day');
            $table->integer('paid_holiday_information_id');
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
        Schema::dropIfExists('work_time_per_day_change_milestones');
    }
}
