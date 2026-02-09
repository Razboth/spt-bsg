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
        Schema::table('target_units', function (Blueprint $table) {
            $table->char('type', 1)->default(1)->after('id')->comment("1: Sales Account, 2: Sales Activity");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('target_units', function (Blueprint $table) {
            //
        });
    }
};
