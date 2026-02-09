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
        Schema::create('employee_organizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('structure_id');
            $table->foreignId('organization_id')->nullable();
            $table->string('code', 15);
            $table->string('name');
            $table->string('description')->nullable();
            $table->char('level', 2);
            $table->integer('order')->default(0);
            $table->char('status', 1);
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
        Schema::dropIfExists('employee_organizations');
    }
};
