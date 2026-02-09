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
        Schema::create('realizations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('plan_id');
            $table->foreignUuid('agent_id');
            $table->foreignUuid('customer_id');
            $table->string('title', 100);
            $table->date('date');
            $table->time('time');
            $table->foreignUuid('product_id');
            $table->foreignUuid('type_id');
            $table->string('note')->nullable();
            $table->text('filename')->nullable();
            $table->integer('closing_amount')->default(0)->nullable();
            $table->string('account_number', 15)->nullable();
            $table->foreignUuid('approved_by')->nullable();
            $table->char('approved_status', 1)->nullable();
            $table->date('approved_date')->nullable();
            $table->text('approved_note')->nullable();
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
        Schema::dropIfExists('realizations');
    }
};
