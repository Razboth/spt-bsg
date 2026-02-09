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
        Schema::create('payroll_pkp_rates', function (Blueprint $table) {
            $table->id();
            $table->char('year', 4);
            $table->string('minimal_amount', 20);
            $table->string('maximal_amount', 20);
            $table->char('rate', 3);
            $table->string('description', 255)->nullable();
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
        Schema::dropIfExists('payroll_pkp_rates');
    }
};
