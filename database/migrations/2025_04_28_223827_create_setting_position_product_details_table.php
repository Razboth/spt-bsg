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
        Schema::create('setting_position_product_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('setting_position_product_id');
            $table->foreignUuid('product_id');
            $table->char('weight', 3);
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
        Schema::dropIfExists('setting_position_product_details');
    }
};
