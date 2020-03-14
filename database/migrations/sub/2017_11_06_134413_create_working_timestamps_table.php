<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkingTimestampsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('working_timestamps', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('enable');
            $table->bigInteger('timestamped_value')->nullable();
            $table->dateTime('raw_date_time_value')->nullable();
            $table->time('processed_time_value')->nullable();
            $table->date('processed_date_value')->nullable();
            $table->integer('timestamped_type');
            $table->integer('registerer_type');
            $table->integer('registerer_id')->nullable();
            $table->integer('work_location_id')->nullable();
            $table->integer('work_address_id')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('employee_working_day_id')->nullable();
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
        Schema::dropIfExists('working_timestamps');
    }
}
