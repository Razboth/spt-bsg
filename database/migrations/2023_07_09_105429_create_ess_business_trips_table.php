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
        Schema::create('ess_business_trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id');
            $table->string('number', 20);
            $table->date('date');
            $table->string('subject', 100);
            $table->foreignId('category_id');
            $table->string('city', 100)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->char('days', 1);
            $table->string('depart_transport', 100)->nullable();
            $table->string('return_transport', 100)->nullable();
            $table->char('start_time', 5);
            $table->char('end_time', 5);
            $table->string('amount', 20);
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
        Schema::dropIfExists('ess_business_trips');
    }
};
