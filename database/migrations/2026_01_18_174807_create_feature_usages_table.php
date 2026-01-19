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
        Schema::create('feature_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('feature_name'); // 'instances', 'messages', 'api_calls', etc.
            $table->enum('period_type', ['lifetime', 'day', 'month', 'year'])->default('lifetime');
            $table->datetime('period_start')->nullable(); // For tracking period boundaries
            $table->integer('usage_count')->default(0);
            $table->timestamps();

            // Indexes for efficient queries
            $table->index(['user_id', 'feature_name', 'period_type']);
            $table->index(['subscription_id', 'feature_name']);
            $table->index('period_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_usages');
    }
};
