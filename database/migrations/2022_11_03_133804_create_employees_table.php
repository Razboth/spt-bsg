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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('employee_number');
            $table->string('nickname', 50)->nullable();
            $table->string('place_of_birth', 50)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('identity_number', 50)->nullable();
            $table->text('address')->nullable();
            $table->text('identity_address')->nullable();
            $table->foreignId('marital_status_id')->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->date('join_date');
            $table->date('leave_date')->nullable();
            $table->string('photo')->nullable();
            $table->string('identity_file')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile_phone_number', 20)->nullable();
            $table->foreignId('status_id');
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
        Schema::dropIfExists('employees');
    }
};
