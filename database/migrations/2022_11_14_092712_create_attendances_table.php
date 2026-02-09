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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id');
            $table->foreignId('location_id')->nullable();
            $table->char('type', 1)->comment('1: WFO, 2: WFH, 3: Bebas/Dinas');
            $table->date('start_date')->nullable();
            $table->time('start_time')->nullable();
            $table->decimal('start_latitude', 20, 8)->nullable();
            $table->decimal('start_longitude', 20, 8)->nullable();
            $table->text('start_image')->nullable();
            $table->text('start_address')->nullable();
            $table->date('end_date')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('end_latitude', 20, 8)->nullable();
            $table->decimal('end_longitude', 20, 8)->nullable();
            $table->text('end_image')->nullable();
            $table->text('end_address')->nullable();
            $table->time('duration')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('attendances');
    }
};
