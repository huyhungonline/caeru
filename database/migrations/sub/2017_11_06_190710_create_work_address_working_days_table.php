<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkAddressWorkingDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_address_working_days', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('work_address_id');
            $table->date('date');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['work_address_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_address_working_days');
    }
}
