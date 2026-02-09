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
            $table->foreignUuid('approved_2_by')->nullable()->after('approved_note');
            $table->char('approved_2_status', 1)->nullable()->after('approved_2_by');
            $table->date('approved_2_date')->nullable()->after('approved_2_status');
            $table->text('approved_2_note')->nullable()->after('approved_2_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_details', function (Blueprint $table) {
            //
        });
    }
};
