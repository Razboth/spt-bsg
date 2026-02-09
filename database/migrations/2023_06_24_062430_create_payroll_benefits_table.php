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
        Schema::create('payroll_benefits', function (Blueprint $table) {
            $table->id();
            $table->char('type', 1)->comment('1 = Financial, 2 = Non-Financial')->default(1);
            $table->foreignId('category_id')->nullable();
            $table->foreignId('payroll_master_id')->nullable();
            $table->foreignId('employee_type_id')->nullable();
            $table->foreignId('marital_id')->nullable();
            $table->foreignId('position_id')->nullable();
            $table->foreignId('rank_id')->nullable();
            $table->foreignId('grade_id')->nullable();
            $table->foreignId('location_id')->nullable();
            $table->foreignId('unit_id')->nullable();
            $table->char('work_period', 3)->nullable();
            $table->char('reset_period', 3)->nullable();
            $table->string('amount', 20);
            $table->string('amount_prorate', 20);
            $table->string('amount_probation', 20);
            $table->text('description')->nullable();
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
        Schema::dropIfExists('payroll_benefits');
    }
};
