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
        Schema::create('ess_medicals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id');
            $table->string('number', 20);
            $table->date('date');
            $table->foreignId('category_id');
            $table->date('claim_date');
            $table->string('hospital_name', 100)->nullable();
            $table->string('doctor_name', 100)->nullable();
            $table->string('amount', 20);
            $table->string('balance', 20);
            $table->string('limit', 20);
            $table->text('filename')->nullable();
            $table->text('description')->nullable();
            $table->string('approved_by', 20)->nullable();
            $table->char('approved_status', 1)->nullable();
            $table->date('approved_date')->nullable();
            $table->string('approved_note', 100)->nullable();
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
        Schema::dropIfExists('ess_medicals');
    }
};
