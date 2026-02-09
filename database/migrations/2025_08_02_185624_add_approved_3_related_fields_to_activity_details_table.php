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
            $table->foreignUuid('approved_3_by')->nullable()->after('approved_2_note');
            $table->char('approved_3_status', 1)->nullable()->after('approved_3_by');
            $table->text('approved_3_note')->nullable()->after('approved_3_status');
            $table->date('approved_3_date')->nullable()->after('approved_3_note');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_details', function (Blueprint $table) {
            $table->dropColumn('approved_3_by');
            $table->dropColumn('approved_3_status');
            $table->dropColumn('approved_3_note');
            $table->dropColumn('approved_3_date');
        });
    }
};
