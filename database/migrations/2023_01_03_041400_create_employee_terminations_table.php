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
        Schema::create('employee_terminations', function (Blueprint $table) {
            $table->id();
            $table->string('number', 50);
            $table->foreignId('employee_id');
            $table->foreignId('reason_id');
            $table->foreignId('type_id');
            $table->date('date');
            $table->text('description')->nullable();
            $table->text('filename')->nullable();
            $table->date('effective_date');
            $table->text('note')->nullable();
            $table->string('approved_by', 20)->nullable();
            $table->char('approved_status', 1)->nullable();
            $table->date('approved_date')->nullable();
            $table->text('approved_note')->nullable();
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
        Schema::dropIfExists('employee_terminations');
    }
};
