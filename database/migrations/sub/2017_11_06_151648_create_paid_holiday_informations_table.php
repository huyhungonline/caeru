<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaidHolidayInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paid_holiday_informations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id');
            $table->date('period_start');
            $table->date('period_end');
            $table->float('work_time_per_day');
            $table->float('attendance_rate')->nullable();
            $table->float('provided_paid_holidays')->nullable();
            $table->float('carried_forward_paid_holidays')->nullable();
            $table->float('consumed_paid_holidays')->nullable();
            $table->float('available_paid_holidays')->nullable();
            $table->text('note')->nullable();
            $table->date('last_modified_date')->nullable();
            $table->integer('last_modified_manager_id')->nullable();
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
        Schema::dropIfExists('paid_holiday_informations');
    }
}
