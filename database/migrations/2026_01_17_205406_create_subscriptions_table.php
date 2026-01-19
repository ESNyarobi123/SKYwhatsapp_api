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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('plan_name');
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->timestamp('expires_at');
            $table->date('renewal_date')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('payment_provider', ['mpesa', 'tigopesa', 'airtelmoney', 'stripe'])->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
