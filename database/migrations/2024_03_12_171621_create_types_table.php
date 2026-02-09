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
        Schema::create('types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 150);
            $table->char('point', 2)->nullable();
            $table->text('description')->nullable();
            $table->text('icon')->nullable();
            $table->string('color', 10)->nullable();
            $table->integer('order')->default(0);
            $table->char('status', 1)->default('t');
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
        Schema::dropIfExists('types');
    }
};
