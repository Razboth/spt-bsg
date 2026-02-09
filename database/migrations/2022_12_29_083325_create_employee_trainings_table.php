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
        Schema::create('employee_trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id');
            $table->string('certificate_number', 100)->nullable();
            $table->string('subject', 100);
            $table->string('institution', 100);
            $table->foreignId('category_id')->nullable();
            $table->foreignId('type_id')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('location', 100)->nullable();
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
        Schema::dropIfExists('employee_trainings');
    }
};
