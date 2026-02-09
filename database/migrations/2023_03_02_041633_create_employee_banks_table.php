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
        Schema::create('employee_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id');
            $table->foreignId('bank_id');
            $table->string('branch', 100)->nullable();
            $table->string('account_number', 100)->nullable();
            $table->string('account_name', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('status', 1)->default('t');
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
        Schema::dropIfExists('employee_banks');
    }
};
