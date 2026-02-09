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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('npwp_number', 20)->nullable()->after('attendance_pin');
            $table->date('npwp_date')->nullable()->after('npwp_number');
            $table->string('bpjs_number', 20)->nullable()->after('npwp_date');
            $table->date('bpjs_date')->nullable()->after('bpjs_number');
            $table->string('bpjs_tk_number', 20)->nullable()->after('bpjs_date');
            $table->date('bpjs_tk_date')->nullable()->after('bpjs_tk_number');
            $table->char("blood_type", 3)->nullable()->after('bpjs_tk_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            //
        });
    }
};
