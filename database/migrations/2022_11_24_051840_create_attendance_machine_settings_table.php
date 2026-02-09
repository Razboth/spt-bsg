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
        Schema::create('attendance_machine_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id');
            $table->string('serial_number', 100)->nullable();
            $table->string('name', 100);
            $table->string('ip_address', 50);
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->char('status', 1)->default('t');
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
        Schema::dropIfExists('attendance_machine_settings');
    }
};
