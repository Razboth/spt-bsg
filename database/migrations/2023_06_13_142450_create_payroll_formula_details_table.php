<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_formula_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_formula_id');
            $table->foreignId('component_id');
            $table->char('operator', 1);
            $table->string('value', 20);
            $table->char('next_operator', 1);
            $table->char('status', 1);
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
        Schema::dropIfExists('payroll_formula_details');
    }
};