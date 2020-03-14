<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConcludedEmployeeWorkingInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concluded_employee_working_informations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id');
            $table->date('date');
            $table->time('schedule_start_work_time')->nullable();
            $table->time('schedule_end_work_time')->nullable();
            $table->integer('schedule_break_time')->nullable();
            $table->integer('schedule_night_break_time')->nullable();
            $table->time('schedule_working_hour')->nullable();
            $table->integer('planned_work_location_id')->nullable();
            $table->integer('real_work_location_id')->nullable();
            $table->integer('planned_work_address_id')->nullable();
            $table->integer('real_work_address_id')->nullable();
            $table->string('work_status')->nullable();
            $table->string('rest_status')->nullable();
            $table->time('paid_rest_time_start')->nullable();
            $table->time('paid_rest_time_end')->nullable();
            $table->time('real_paid_time')->nullable();
            $table->time('current_work_time_per_day')->nullable();
            $table->time('planned_start_work_time')->nullable();
            $table->time('timestamped_start_work_time')->nullable();
            $table->time('real_start_work_time')->nullable();
            $table->time('planned_end_work_time')->nullable();
            $table->time('timestamped_end_work_time')->nullable();
            $table->time('real_end_work_time')->nullable();
            $table->time('planned_working_hour')->nullable();
            $table->time('real_working_hour')->nullable();
            $table->text('note')->nullable();
            $table->time('planned_early_arrive_start')->nullable();
            $table->time('real_early_arrive_start')->nullable();
            $table->time('planned_early_arrive_end')->nullable();
            $table->time('real_early_arrive_end')->nullable();
            $table->integer('planned_late_time')->nullable();
            $table->integer('real_late_time')->nullable();
            $table->time('planned_work_span_start')->nullable();
            $table->time('real_work_span_start')->nullable();
            $table->time('planned_work_span_end')->nullable();
            $table->time('real_work_span_end')->nullable();
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
            $table->time('planned_overtime_start')->nullable();
            $table->time('real_overtime_start')->nullable();
            $table->time('planned_overtime_end')->nullable();
            $table->time('real_overtime_end')->nullable();
            $table->integer('last_modified_person_id')->nullable();
            $table->string('last_modified_person_type')->nullable();
            $table->integer('basic_salary')->nullable();
            $table->integer('night_salary')->nullable();
            $table->integer('overtime_salary')->nullable();
            $table->integer('deduction_salary')->nullable();
            $table->integer('night_deduction_salary')->nullable();
            $table->integer('monthly_traffic_expense')->nullable();
            $table->integer('daily_traffic_expense')->nullable();
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
        Schema::dropIfExists('concluded_employee_working_informations');
    }
}
