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
        Schema::table('activity_details', function (Blueprint $table) {
            $table->text('plan_document')->nullable()->after('plan_filename');
            $table->text('realization_document')->nullable()->after('realization_filename');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_details', function (Blueprint $table) {
            $table->dropColumn('plan_document');
            $table->dropColumn('realization_document');
        });
    }
};
