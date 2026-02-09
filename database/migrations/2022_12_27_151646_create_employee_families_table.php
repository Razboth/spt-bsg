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
        Schema::create('employee_families', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id');
            $table->foreignId('relationship_id');
            $table->string('name');
            $table->string('identity_number', 20)->nullable();
            $table->char('gender', '1')->default('m');
            $table->date('birth_date')->nullable();
            $table->string('birth_place', 50)->nullable();
            $table->text('filename')->nullable();
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
        Schema::dropIfExists('employee_families');
    }
};
