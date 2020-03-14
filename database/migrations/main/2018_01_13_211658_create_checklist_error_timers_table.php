<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChecklistErrorTimersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklist_error_timers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_code', 32);
            $table->integer('employee_id');
            $table->date('date');
            $table->integer('timestamp_error_type');
            $table->dateTime('due_time');
            $table->integer('employee_working_information_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checklist_error_timers');
    }
}
