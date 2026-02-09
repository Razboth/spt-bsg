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
        Schema::create('activity_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('type', 1);
            $table->foreignUuid('activity_id');
            $table->foreignUuid('type_id');
            $table->date('plan_date')->nullable();
            $table->time('plan_time')->nullable();
            $table->text('plan_note')->nullable();
            $table->text('plan_filename')->nullable();
            $table->date('realization_date')->nullable();
            $table->time('realization_time')->nullable();
            $table->text('realization_note')->nullable();
            $table->text('realization_filename')->nullable();
            $table->integer('closing_amount')->default(0)->nullable();
            $table->string('account_number', 15)->nullable();
            $table->foreignUuid('approved_by')->nullable();
            $table->char('approved_status', 1)->nullable();
            $table->date('approved_date')->nullable();
            $table->text('approved_note')->nullable();
            $table->double('latitude', 10, 6)->nullable();
            $table->double('longitude', 10, 6)->nullable();
            $table->text('address')->nullable();
            $table->foreignUuid('created_by')->nullable();
            $table->foreignUuid('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_details');
    }
};
