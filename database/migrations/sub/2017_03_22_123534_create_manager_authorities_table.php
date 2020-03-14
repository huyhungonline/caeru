<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManagerAuthoritiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manager_authorities', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('manager_id');
            $table->tinyInteger('company_information')->default(\App\ManagerAuthority::CHANGE);
            $table->tinyInteger('work_location_information')->default(\App\ManagerAuthority::CHANGE);
            $table->tinyInteger('work_address_information')->default(\App\ManagerAuthority::CHANGE);
            $table->tinyInteger('employee_basic_information')->default(\App\ManagerAuthority::CHANGE);
            $table->tinyInteger('employee_work_information')->default(\App\ManagerAuthority::CHANGE);
            $table->tinyInteger('calendar_setting')->default(\App\ManagerAuthority::CHANGE);
            $table->tinyInteger('setting')->default(\App\ManagerAuthority::CHANGE);
            $table->tinyInteger('statuses_setting')->default(\App\ManagerAuthority::CHANGE);
            $table->tinyInteger('department_type_setting')->default(\App\ManagerAuthority::CHANGE);
            $table->tinyInteger('work_data_management')->default(\App\ManagerAuthority::BROWSE);
            $table->tinyInteger('work_data_search')->default(\App\ManagerAuthority::BROWSE);
            $table->tinyInteger('work_data_calculation')->default(\App\ManagerAuthority::BROWSE);
            $table->tinyInteger('work_data_detail')->default(\App\ManagerAuthority::BROWSE);
            $table->tinyInteger('work_data_personal_detail')->default(\App\ManagerAuthority::BROWSE);
            $table->boolean('work_data_modify')->default(true);
            $table->boolean('work_data_modify_request_confirm')->default(true);
            $table->tinyInteger('work_data_paid_holiday_management')->default(\App\ManagerAuthority::CHANGE);
            $table->tinyInteger('work_data_paid_holiday_detail')->default(\App\ManagerAuthority::CHANGE);
            $table->tinyInteger('work_data_addresses')->default(\App\ManagerAuthority::BROWSE);
            $table->tinyInteger('work_data_address_detail')->default(\App\ManagerAuthority::CHANGE);
            $table->tinyInteger('work_data_address_work_detail')->default(\App\ManagerAuthority::CHANGE);
            $table->boolean('approval_level_one')->default(true);
            $table->boolean('approval_level_two')->default(true);
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
        Schema::dropIfExists('manager_authorities');
    }
}
