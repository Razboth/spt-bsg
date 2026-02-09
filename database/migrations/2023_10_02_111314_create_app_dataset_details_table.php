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
        Schema::create('app_dataset_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dataset_id');
            $table->string('title', 100);
            $table->string('code', 20);
            $table->string('example', 100)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_dataset_details');
    }
};
