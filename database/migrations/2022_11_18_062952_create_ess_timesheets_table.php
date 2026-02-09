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
        Schema::create('ess_timesheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id');
            $table->string('activity');
            $table->string('output');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->time('duration');
            $table->string('volume', 30);
            $table->string('type', 50);
            $table->text('description')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
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
        Schema::dropIfExists('ess_timesheets');
    }
};
