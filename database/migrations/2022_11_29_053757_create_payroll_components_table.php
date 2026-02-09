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
        Schema::create('payroll_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_id');
            $table->char('type', 1)->comment('a => Allowance, d => Deduction');
            $table->string('code', 10);
            $table->string('name', 150);
            $table->string('description', 255)->nullable();
            $table->char('status', 1)->comment('t => Active, f => Inactive');
            $table->char('calculation_type', 1)->comment('1 = table, 2 = proses, 3 = fixed');
            $table->char('calculation_cut_off', 1)->comment('1 = start month to end month, 2 = custom');
            $table->char('calculation_cut_off_date_start', 2)->nullable();
            $table->char('calculation_cut_off_date_end', 2)->nullable();
            $table->text('calculation_description')->nullable();
            $table->string('calculation_amount', 20)->nullable();
            $table->string('calculation_amount_min', 20)->nullable();
            $table->string('calculation_amount_max', 20)->nullable();
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
        Schema::dropIfExists('payroll_components');
    }
};
