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
        Schema::table('app_master_data', function (Blueprint $table) {
            $table->foreignUuid('parent_id')->nullable()->change();
        });

        Schema::table('app_master_categories', function (Blueprint $table) {
            $table->foreignUuid('parent_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('foreign_uuid_in_app_master_data_table_and_app_master_categories', function (Blueprint $table) {
            //
        });
    }
};
