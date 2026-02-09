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
        Schema::create('change_customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('number', 20);
            $table->date('date');
            $table->foreignUuid('from_unit_id');
            $table->foreignUuid('from_agent_id');
            $table->foreignUuid('customer_id');
            $table->foreignUuid('to_unit_id');
            $table->foreignUuid('to_agent_id');
            $table->text('description')->nullable();
            $table->char('approved_status', 1)->nullable();
            $table->string('approved_by', 36)->nullable();
            $table->date('approved_date')->nullable();
            $table->text('approved_note')->nullable();
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
        Schema::dropIfExists('change_customers');
    }
};
