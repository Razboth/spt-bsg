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
        Schema::table('app_number_formats', function (Blueprint $table) {
            $table->string('var1_value', 10)->nullable()->change();
            $table->string('var2_value', 10)->nullable()->change();
            $table->string('var3_value', 10)->nullable()->change();
            $table->string('var4_value', 10)->nullable()->change();
            $table->string('var5_value', 10)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_number_formats', function (Blueprint $table) {
            //
        });
    }
};
