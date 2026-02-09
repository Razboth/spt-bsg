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
        Schema::create('app_moduls', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('target', 50);
            $table->text('description')->nullable();
            $table->string('icon', 20)->nullable();
            $table->string('order');
            $table->string('status', 1)->default('t');
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
        Schema::dropIfExists('app_moduls');
    }
};
