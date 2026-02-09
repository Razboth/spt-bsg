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
        Schema::create('spt_scores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('score', 1);
            $table->string('minimum_value', 5)->nullable();
            $table->string('maximum_value', 5)->nullable();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('spt_scores');
    }
};
