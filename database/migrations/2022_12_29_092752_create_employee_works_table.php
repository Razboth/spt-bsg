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
        Schema::create('employee_works', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id');
            $table->string('company', 100);
            $table->string('position', 100);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('city', 100)->nullable();
            $table->text('job_desc')->nullable();
            $table->text('description')->nullable();
            $table->text('filename')->nullable();
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
        Schema::dropIfExists('employee_works');
    }
};
