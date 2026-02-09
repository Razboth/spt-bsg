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
        Schema::create('setting_booms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('unit_id');
            $table->string('month', 2);
            $table->string('year', 4);
            $table->string('target', 10)->nullable();
            $table->string('realization', 10)->nullable();
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
        Schema::dropIfExists('setting_booms');
    }
};
