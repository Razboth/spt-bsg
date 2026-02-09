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
        Schema::create('app_number_formats', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('code', 20);
            $table->char('delimiter', 1);
            $table->string('var1_type', 10);
            $table->string('var1_value', 3);
            $table->string('var2_type', 10)->nullable();
            $table->string('var2_value', 3)->nullable();
            $table->string('var3_type', 10)->nullable();
            $table->string('var3_value', 3)->nullable();
            $table->string('var4_type', 10)->nullable();
            $table->string('var4_value', 3)->nullable();
            $table->string('var5_type', 10)->nullable();
            $table->string('var5_value', 3)->nullable();
            $table->string('example', 50);
            $table->text('description')->nullable();
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
        Schema::dropIfExists('app_number_formats');
    }
};
