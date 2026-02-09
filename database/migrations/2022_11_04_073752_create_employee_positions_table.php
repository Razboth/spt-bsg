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
        Schema::create('employee_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id');
            $table->foreignId('employee_type_id');
            $table->foreignId('position_id');
            $table->foreignId('rank_id');
            $table->foreignId('grade_id')->nullable();
            $table->date('sk_date')->nullable();
            $table->date('sk_number')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->foreignId('unit_id')->nullable();
            $table->foreignId('location_id');
            $table->foreignId('shift_id')->nullable();
            $table->char('status', 1);
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
        Schema::dropIfExists('employee_positions');
    }
};
