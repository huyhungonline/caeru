<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeWorkingInformationSnapshotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_working_information_snapshots', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('modify_request_id');
            $table->integer('timestamp_start_work_work_location_id')->nullable();
            $table->date('timestamp_start_work_date')->nullable();
            $table->time('timestamp_start_work_time')->nullable();
            $table->integer('timestamp_end_work_work_location_id')->nullable();
            $table->date('timestamp_end_work_date')->nullable();
            $table->time('timestamp_end_work_time')->nullable();
            $table->integer('work_status_id')->nullable();
            $table->integer('rest_status_id')->nullable();
            $table->date('switch_planned_schedule_target')->nullable();
            $table->integer('planned_work_location_id')->nullable();
            $table->integer('real_work_location_id')->nullable();
            $table->integer('planned_early_arrive_start_time')->nullable();
            $table->integer('real_early_arrive_start_time')->nullable();
            $table->integer('planned_early_arrive_end_time')->nullable();
            $table->integer('real_early_arrive_end_time')->nullable();
            $table->integer('planned_work_span_start_time')->nullable();
            $table->integer('real_work_span_start_time')->nullable();
            $table->integer('planned_work_span_end_time')->nullable();
            $table->integer('real_work_span_end_time')->nullable();
            $table->integer('planned_overtime_start_time')->nullable();
            $table->integer('real_overtime_start_time')->nullable();
            $table->integer('planned_overtime_end_time')->nullable();
            $table->integer('real_overtime_end_time')->nullable();
            $table->integer('planned_break_time')->nullable();
            $table->integer('real_break_time')->nullable();
            $table->integer('planned_night_break_time')->nullable();
            $table->integer('real_night_break_time')->nullable();
            $table->integer('planned_late_time')->nullable();
            $table->integer('real_late_time')->nullable();
            $table->integer('planned_early_leave_time')->nullable();
            $table->integer('real_early_leave_time')->nullable();
            $table->integer('planned_go_out_time')->nullable();
            $table->integer('real_go_out_time')->nullable();
            $table->text('requester_note')->nullable();
            $table->integer('approver_id')->nullable();
            $table->integer('approver_type')->nullable();
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
        Schema::dropIfExists('employee_working_information_snapshots');
    }
}
