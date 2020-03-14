<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('managers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('presentation_id')->unique();
            $table->string('first_name');
            $table->string('first_name_furigana');
            $table->string('last_name');
            $table->string('last_name_furigana');
            $table->string('password');
            $table->string('telephone', 15)->nullable();
            $table->string('email')->nullable();
            $table->boolean('super')->nullable();
            $table->boolean('enable')->default(1);
            $table->boolean('company_wide_authority')->default(1);
            $table->integer('view_order')->default(config('caeru.unused_view_order'));
            $table->string('remember_token')->nullable();
            $table->integer('company_id');
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
        Schema::dropIfExists('managers');
    }
}
