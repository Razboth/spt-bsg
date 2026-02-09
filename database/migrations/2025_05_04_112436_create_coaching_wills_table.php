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
        Schema::create('coaching_wills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('coaching_id');
            $table->string('will', 150);
            $table->date('plan_date');
            $table->date('action_date')->nullable();
            $table->integer('realization')->nullable();
            $table->text('filename')->nullable();
            $table->text('note')->nullable();
            $table->char('validation_status', 1)->nullable();
            $table->text('validation_note')->nullable();
            $table->timestamp('validation_at')->nullable();
            $table->string('validation_by', 36)->nullable();
            $table->string('created_by', 36)->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coaching_wills');
    }
};
