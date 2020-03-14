<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeWorkingInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_working_informations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('planned_schedule_id')->nullable();
            $table->dateTime('schedule_start_work_time')->nullable();
            $table->dateTime('schedule_end_work_time')->nullable();
            $table->integer('schedule_break_time')->nullable();
            $table->integer('schedule_night_break_time')->nullable();
            $table->time('schedule_working_hour')->nullable();
            $table->integer('planned_work_location_id')->nullable();
            $table->integer('real_work_location_id')->nullable();
            $table->integer('planned_work_address_id')->nullable();
            $table->integer('real_work_address_id')->nullable();
            $table->integer('planned_work_status_id')->nullable();
            $table->integer('real_work_status_id')->nullable();
            $table->integer('planned_rest_status_id')->nullable();
            $table->integer('real_rest_status_id')->nullable();
            $table->dateTime('paid_rest_time_start')->nullable();
            $table->dateTime('paid_rest_time_end')->nullable();
            $table->integer('real_paid_rest_time')->nullable();
            $table->integer('real_customized_rest_time')->nullable();
            $table->integer('current_work_time_per_day')->nullable();
            $table->dateTime('planned_start_work_time')->nullable();
            $table->dateTime('timestamped_start_work_time')->nullable();
            $table->dateTime('real_start_work_time')->nullable();
            $table->dateTime('planned_end_work_time')->nullable();
            $table->dateTime('timestamped_end_work_time')->nullable();
            $table->dateTime('real_end_work_time')->nullable();
            $table->time('planned_working_hour')->nullable();
            $table->time('real_working_hour')->nullable();
            $table->text('note')->nullable();
            $table->dateTime('planned_early_arrive_start')->nullable();
            $table->dateTime('real_early_arrive_start')->nullable();
            $table->dateTime('planned_early_arrive_end')->nullable();
            $table->dateTime('real_early_arrive_end')->nullable();
            $table->integer('planned_late_time')->nullable();
            $table->integer('real_late_time')->nullable();
            $table->dateTime('planned_work_span_start')->nullable();
            $table->dateTime('real_work_span_start')->nullable();
            $table->dateTime('planned_work_span_end')->nullable();
            $table->dateTime('real_work_span_end')->nullable();
            $table->time('planned_work_span')->nullable();
            $table->time('real_work_span')->nullable();
            $table->integer('planned_break_time')->nullable();
            $table->integer('real_break_time')->nullable();
            $table->integer('planned_night_break_time')->nullable();
            $table->integer('real_night_break_time')->nullable();
            $table->integer('planned_go_out_time')->nullable();
            $table->integer('real_go_out_time')->nullable();
            $table->integer('planned_early_leave_time')->nullable();
            $table->integer('real_early_leave_time')->nullable();
            $table->dateTime('planned_overtime_start')->nullable();
            $table->dateTime('real_overtime_start')->nullable();
            $table->dateTime('planned_overtime_end')->nullable();
            $table->dateTime('real_overtime_end')->nullable();
            $table->integer('last_modify_person_id')->nullable();
            $table->integer('last_modify_person_type')->nullable();
            $table->integer('basic_salary')->nullable();
            $table->integer('night_salary')->nullable();
            $table->integer('overtime_salary')->nullable();
            $table->integer('deduction_salary')->nullable();
            $table->integer('night_deduction_salary')->nullable();
            $table->integer('monthly_traffic_expense')->nullable();
            $table->integer('daily_traffic_expense')->nullable();
            $table->boolean('manually_modified')->nullable()->default(false);
            $table->integer('employee_working_day_id');
            $table->boolean('temporary')->default(false);
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
        Schema::dropIfExists('employee_working_informations');
    }
}
