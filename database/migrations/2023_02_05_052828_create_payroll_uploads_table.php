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
        Schema::create('payroll_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('code', 15);
            $table->char('month', 2);
            $table->char('year', 4);
            $table->foreignId('component_id');
            $table->foreignId('employee_id');
            $table->string('amount', 20);
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
        Schema::dropIfExists('payroll_uploads');
    }
};
