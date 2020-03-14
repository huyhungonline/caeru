<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkAddressWorkingEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_address_working_employees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('work_address_working_information_id');
            $table->integer('employee_id');
            $table->integer('planned_schedule_id')->nullable();
            $table->integer('employee_working_information_id')->nullable();
            $table->boolean('working_confirm')->default(false);
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
        Schema::dropIfExists('work_address_working_employees');
    }
}
