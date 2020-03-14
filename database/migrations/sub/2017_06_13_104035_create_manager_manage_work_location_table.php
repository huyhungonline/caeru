<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManagerManageWorkLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manager_manage_work_location', function (Blueprint $table) {
            $table->integer('manager_id');
            $table->integer('work_location_id');
            $table->timestamps();
            $table->primary(['manager_id', 'work_location_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manager_manage_work_location');
    }
}
