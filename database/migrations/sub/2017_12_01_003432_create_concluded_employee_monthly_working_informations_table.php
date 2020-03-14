<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConcludedEmployeeMonthlyWorkingInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concluded_employee_monthly_working_informations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('available_work_days');
            $table->integer('planned_work_days');
            $table->integer('real_work_days');
            $table->bigInteger('planned_sum_working_time');
            $table->bigInteger('real_sum_working_time');
            $table->bigInteger('available_work_span_time');
            $table->bigInteger('planned_work_span_time');
            $table->bigInteger('real_work_span_time');
            $table->bigInteger('planned_paid_rest_time');
            $table->bigInteger('real_paid_rest_time');
            $table->bigInteger('planned_unpaid_rest_time');
            $table->bigInteger('real_unpaid_rest_time');
            $table->bigInteger('planned_not_work_time');
            $table->bigInteger('real_not_work_time');
            $table->bigInteger('planned_total_overtime');
            $table->bigInteger('real_total_overtime');
            $table->bigInteger('planned_night_work_time');
            $table->bigInteger('real_night_work_time');
            $table->bigInteger('planned_not_work_days');
            $table->bigInteger('real_not_work_days');
            $table->bigInteger('planned_taken_paid_rest_days');
            $table->bigInteger('real_taken_paid_rest_days');
            $table->bigInteger('planned_remaining_paid_rest_days');
            $table->bigInteger('real_remaining_paid_rest_days');
            $table->bigInteger('current_work_time_per_day');
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
        Schema::dropIfExists('concluded_employee_monthly_working_informations');
    }
}
