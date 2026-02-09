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
        Schema::table('agent_boosters', function (Blueprint $table) {
            $table->string('approved_by', 36)->nullable()->after('value');
            $table->char('approved_status', 1)->nullable()->after('approved_by');
            $table->date('approved_date')->nullable()->after('approved_status');
            $table->text('approved_note')->nullable()->after('approved_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_boosters', function (Blueprint $table) {
            $table->dropColumn('approved_by');
            $table->dropColumn('approved_status');
            $table->dropColumn('approved_date');
            $table->dropColumn('approved_note');
        });
    }
};
