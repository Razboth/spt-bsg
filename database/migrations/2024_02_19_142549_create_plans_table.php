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
        Schema::create('plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('agent_id');
            $table->foreignUuid('customer_id');
            $table->string('title',100);
            $table->string('number', 20);
            $table->date('date');
            $table->time('time');
            $table->foreignUuid('product_id');
            $table->foreignUuid('type_id');
            $table->text('note')->nullable();
            $table->text('filename')->nullable();
            $table->foreignUuid('created_by')->nullable();
            $table->foreignUuid('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
