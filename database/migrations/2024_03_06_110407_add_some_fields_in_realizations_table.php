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
        Schema::table('realizations', function (Blueprint $table) {
            $table->double('latitude', 10, 6)->nullable()->after('approved_note');
            $table->double('longitude', 10, 6)->nullable()->after('latitude');
            $table->text('address')->nullable()->after('longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('realizations', function (Blueprint $table) {
            //
        });
    }
};
