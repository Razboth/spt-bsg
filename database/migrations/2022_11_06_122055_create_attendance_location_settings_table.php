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
        Schema::create('attendance_location_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id');
            $table->text('address')->nullable();
            $table->char('type', 1)->default(0);
            $table->decimal('latitude', 20, 8)->nullable();
            $table->decimal('longitude', 20, 8)->nullable();
            $table->string('radius', 10)->nullable();
            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
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
        Schema::dropIfExists('attendance_location_settings');
    }
};
