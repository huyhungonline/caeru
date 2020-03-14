<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('presentation_id')->unique();
            $table->string('name');
            $table->string('furigana')->nullable();
            $table->integer('work_location_id');
            $table->boolean('enable')->default(1);
            $table->string('postal_code', 12)->nullable();
            $table->integer('todofuken')->nullable();
            $table->string('address')->nullable();
            $table->decimal('login_range')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('telephone', 15)->nullable();
            $table->string('chief_first_name')->nullable();
            $table->string('chief_first_name_furigana')->nullable();
            $table->string('chief_last_name')->nullable();
            $table->string('chief_last_name_furigana')->nullable();
            $table->string('chief_email')->nullable();
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
        Schema::dropIfExists('work_addresses');
    }
}
