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
        Schema::create('employee_education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id');
            $table->foreignId('level_id');
            $table->string('name', 150);
            $table->string('major', 150)->nullable();
            $table->string('essay', 150)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('score', 10)->nullable();
            $table->char('start_year', 4)->nullable();
            $table->char('end_year', 4)->nullable();
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
        Schema::dropIfExists('employee_education');
    }
};
