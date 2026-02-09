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
        Schema::create('attendance_leave_masters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->nullable();
            $table->string('name', 100);
            $table->integer('balance');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('work_period')->nullable();
            $table->char('gender', 1)->default('a')->comment('a = all, f = female, m = male');
            $table->text('description')->nullable();
            $table->char('status', 1)->default('t');
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
        Schema::dropIfExists('attendance_leave_masters');
    }
};
