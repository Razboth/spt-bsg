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
        Schema::create('payroll_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_master_id');
            $table->foreignId('location_id');
            $table->char('month', 2);
            $table->char('year', 4);
            $table->date('process_date');
            $table->string('total_amount', 20)->nullable();
            $table->integer('total_employee')->nullable();
            $table->char('status', 1)->nullable()->comment('0 = Not Processed, 1 = Processed');
            $table->char('approved_status', 1)->nullable()->comment('0 = Not Approved, 1 = Approved, 2 = Rejected');
            $table->string('approved_by', 50)->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->string('approved_note', 255)->nullable();
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
        Schema::dropIfExists('payroll_processes');
    }
};