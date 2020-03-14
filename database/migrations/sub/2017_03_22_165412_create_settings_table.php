<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->nullable();
            $table->integer('work_location_id')->nullable();
            $table->tinyInteger('timezone')->nullable()->default(null);
            $table->tinyInteger('salary_accounting_day')->nullable()->default(null);
            $table->tinyInteger('pay_month')->nullable()->default(null);
            $table->tinyInteger('pay_day')->nullable()->default(null);
            $table->tinyInteger('start_day_of_week')->nullable()->default(null);
            $table->tinyInteger('law_rest_day_mode')->nullable()->default(null);
            $table->tinyInteger('start_time_round_up')->nullable()->default(null);
            $table->tinyInteger('end_time_round_down')->nullable()->default(null);
            $table->tinyInteger('break_time_round_up')->nullable()->default(null);
            $table->integer('start_time_diff_limit')->nullable()->default(null);
            $table->integer('end_time_diff_limit')->nullable()->default(null);
            $table->tinyInteger('go_out_button_usage')->nullable()->default(null);
            $table->boolean('display_go_out_time')->nullable()->default(null);
            $table->boolean('use_overtime_button')->nullable()->default(null);
            $table->integer('paid_holiday_after_joined_period')->nullable()->default(null);
            $table->integer('paid_holiday_first_time_normal_type')->nullable()->default(null);
            $table->integer('paid_holiday_first_time_4wdpw_type')->nullable()->default(null);
            $table->integer('paid_holiday_first_time_3wdpw_type')->nullable()->default(null);
            $table->integer('paid_holiday_first_time_2wdpw_type')->nullable()->default(null);
            $table->integer('paid_holiday_first_time_1wdpw_type')->nullable()->default(null);
            $table->string('paid_holiday_increase_rate_normal_type')->nullable()->default(null);
            $table->string('paid_holiday_increase_rate_4wdpw_type')->nullable()->default(null);
            $table->string('paid_holiday_increase_rate_3wdpw_type')->nullable()->default(null);
            $table->string('paid_holiday_increase_rate_2wdpw_type')->nullable()->default(null);
            $table->string('paid_holiday_increase_rate_1wdpw_type')->nullable()->default(null);
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
        Schema::dropIfExists('settings');
    }
}
