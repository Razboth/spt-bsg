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
        Schema::table('app_parameters', function (Blueprint $table) {
            $table->uuid('id')->change();
        });

        Schema::table('app_number_formats', function (Blueprint $table) {
            $table->uuid('id')->change();
        });

        Schema::table('app_datasets', function (Blueprint $table) {
            $table->uuid('id')->change();
        });

        Schema::table('app_dataset_details', function (Blueprint $table) {
            $table->uuid('id')->change();
            $table->foreignUuid('dataset_id')->change();
        });

        Schema::table('app_master_templates', function (Blueprint $table) {
            $table->uuid('id')->change();
            $table->foreignUuid('dataset_id')->change();
            $table->foreignUuid('page_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('some_tables_and_models', function (Blueprint $table) {
            //
        });
    }
};
