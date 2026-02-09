<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 150);
            $table->string('nickname', 15)->nullable();
            $table->string('employee_number', 30)->nullable();
            $table->string('identity_number', 20)->nullable();
            $table->string('birth_place', 50)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->char('gender', 1)->nullable();
            $table->foreignUuid('status_id');
            $table->foreignUuid('type_id');
            $table->foreignUuid('position_id');
            $table->date('join_date');
            $table->date('leave_date')->nullable();
            $table->text('identity_file')->nullable();
            $table->text('photo')->nullable();
            $table->string('created_by', 36)->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
