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
        Schema::create('app_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable();
            $table->foreignId('app_modul_id');
            $table->foreignId('app_sub_modul_id');
            $table->string('name', 150);
            $table->string('target', 50);
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable();
            $table->string('parameter', 50)->nullable();
            $table->char('full_screen', 1)->default('f');
            $table->char('status', 1)->default('t');
            $table->integer('order');
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
        Schema::dropIfExists('app_menus');
    }
};
