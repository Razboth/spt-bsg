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
        Schema::table('activities', function (Blueprint $table) {
            $table->string('approved_by', 36)->nullable()->after('sub_product_id');
            $table->char('approved_status', 1)->nullable()->after('approved_by');
            $table->date('approved_date')->nullable()->after('approved_status');
            $table->text('approved_note')->nullable()->after('approved_date');

            $table->string('approved_2_by', 36)->nullable()->after('approved_note');
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
        Schema::table('activities', function (Blueprint $table) {
            //
        });
    }
};
