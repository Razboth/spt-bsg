<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_infos', function (Blueprint $table) {
            $table->id();
            $table->string('title');

            $table->string('primary_color', 10);
            $table->string('light_primary_color', 10);
            $table->string('background_light_primary_color', 10);

            $table->text('login_page_title')->nullable();
            $table->text('login_page_subtitle')->nullable();
            $table->text('login_page_description')->nullable();
            $table->text('login_page_logo')->nullable();
            $table->text('login_page_background_image')->nullable();
            $table->text('login_page_image')->nullable();

            $table->string('footer_text')->nullable();
            $table->char('year', 4);
            $table->char('app_version', 5);
            $table->text('app_logo')->nullable();
            $table->text('app_logo_small')->nullable();
            $table->text('app_icon')->nullable();

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
        Schema::dropIfExists('app_infos');
    }
};
