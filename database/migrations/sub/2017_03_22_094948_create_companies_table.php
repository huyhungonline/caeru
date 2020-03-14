<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 32);
            $table->string('name');
            $table->string('furigana');
            $table->string('postal_code', 12)->nullable();
            $table->integer('todofuken')->nullable();
            $table->string('address')->nullable();
            $table->string('telephone', 15)->nullable();
            $table->string('fax', 15)->nullable();
            $table->string('ceo_first_name')->nullable();
            $table->string('ceo_first_name_furigana')->nullable();
            $table->string('ceo_last_name')->nullable();
            $table->string('ceo_last_name_furigana')->nullable();
            $table->string('ceo_email')->nullable();
            $table->string('billing_person_first_name')->nullable();
            $table->string('billing_person_first_name_furigana')->nullable();
            $table->string('billing_person_last_name')->nullable();
            $table->string('billing_person_last_name_furigana')->nullable();
            $table->string('billing_person_email')->nullable();
            $table->time('date_separate_time')->default('00:00:00');
            $table->integer('date_separate_type');
            $table->boolean('use_address_system')->default(false);
            $table->boolean('initial_setting_completed')->default(false);
            $table->boolean('initial_calendar_completed')->default(false);
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
        Schema::dropIfExists('companies');
    }
}
