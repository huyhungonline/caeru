<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeWorkingInformationModifyRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_working_information_modify_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id');
            $table->integer('employee_working_information_id');
            $table->integer('before_working_information_snapshot_id');
            $table->integer('after_working_information_snapshot_id');
            $table->integer('request_status');
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
        Schema::dropIfExists('employee_working_information_modify_requests');
    }
}
