<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_corrections', function (Blueprint $table) {
            $table->time('duration')->nullable()->after('end_time');
            $table->time('actual_duration')->nullable()->after('actual_end_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_corrections', function (Blueprint $table) {
            //
        });
    }
};
