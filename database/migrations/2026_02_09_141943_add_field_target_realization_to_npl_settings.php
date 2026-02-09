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
        Schema::table('npl_settings', function (Blueprint $table) {
            $table->string('target', 10)->nullable()->after('value');
            $table->string('realization', 10)->nullable()->after('target');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('npl_settings', function (Blueprint $table) {
            $table->dropColumn('target');
            $table->dropColumn('realization');
        });
    }
};
