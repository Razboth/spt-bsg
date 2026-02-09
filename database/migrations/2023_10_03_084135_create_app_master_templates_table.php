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
        Schema::create('app_master_templates', function (Blueprint $table) {
            $table->id();
            $table->char('type', 1);
            $table->foreignId('dataset_id')->nullable();
            $table->foreignId('page_id')->nullable();
            $table->string('name');
            $table->string('code');
            $table->longText('content')->nullable();
            $table->char('margin_bottom', 2)->nullable();
            $table->char('margin_top', 2)->nullable();
            $table->char('margin_left', 2)->nullable();
            $table->char('margin_right', 2)->nullable();
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
        Schema::dropIfExists('app_master_templates');
    }
};
