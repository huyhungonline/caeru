<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('presentation_id')->unique();
            $table->string('first_name');
            $table->string('first_name_furigana');
            $table->string('last_name');
            $table->string('last_name_furigana');
            $table->string('password')->nullable();
            $table->date('birthday');
            $table->tinyInteger('gender');
            $table->string('postal_code', 12)->nullable();
            $table->integer('todofuken')->nullable();
            $table->string('address')->nullable();
            $table->string('telephone', 15)->nullable();
            $table->string('email')->nullable();
            $table->integer('work_location_id');
            $table->date('joined_date');
            $table->integer('department_id')->nullable();
            $table->tinyInteger('schedule_type');
            $table->tinyInteger('employment_type');
            $table->tinyInteger('salary_type');
            $table->tinyInteger('work_status');
            $table->date('resigned_date')->nullable();
            $table->string('card_registration_number')->unique();
            $table->string('card_number')->nullable();
            $table->boolean('paid_holiday_exception')->default(false);
            $table->date('holidays_update_day')->nullable();
            $table->float('work_time_per_day')->nullable();
            $table->integer('holiday_bonus_type')->nullable()->default();
            $table->integer('view_order')->default(config('caeru.unused_view_order'));
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
        Schema::dropIfExists('employees');
    }
}
