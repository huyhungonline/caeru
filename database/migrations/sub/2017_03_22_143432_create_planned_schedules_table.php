<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlannedSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planned_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id');
            $table->integer('work_location_id');
            $table->integer('work_address_id')->nullable();
            $table->boolean('prioritize_company_calendar')->default(true);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            // $table->tinyInteger('frequency_type')->nullable();
            $table->string('working_days_of_week');
            $table->boolean('rest_on_holiday')->default(false);
            $table->time('start_work_time')->nullable();
            $table->time('end_work_time')->nullable();
            $table->integer('break_time')->nullable();
            $table->integer('night_break_time')->nullable();
            $table->time('working_hour')->nullable();
            $table->boolean('candidating_type')->nullable();
            $table->integer('candidate_number')->nullable();
            $table->tinyInteger('normal_salary_type')->nullable();
            $table->integer('normal_salary')->nullable();
            $table->integer('normal_night_salary')->nullable();
            $table->integer('normal_overtime_salary')->nullable();
            $table->integer('normal_deduction_salary')->nullable();
            $table->integer('normal_night_deduction_salary')->nullable();
            $table->tinyInteger('holiday_salary_type')->nullable();
            $table->integer('holiday_salary')->nullable();
            $table->integer('holiday_night_salary')->nullable();
            $table->integer('holiday_overtime_salary')->nullable();
            $table->integer('holiday_deduction_salary')->nullable();
            $table->integer('holiday_night_deduction_salary')->nullable();
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
        Schema::dropIfExists('planned_schedules');
    }
}
