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
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('payment_method', ['zenopay_card', 'zenopay_mobile', 'paypal', 'trc20', 'mpesa', 'tigopesa', 'airtelmoney', 'stripe'])->nullable()->after('provider');
            $table->string('order_id')->nullable()->after('reference');
            $table->string('tx_ref')->nullable()->after('order_id');
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->nullable()->after('status');
            
            $table->index('order_id');
            $table->index('tx_ref');
            $table->index('verification_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['tx_ref']);
            $table->dropIndex(['verification_status']);
            $table->dropColumn(['payment_method', 'order_id', 'tx_ref', 'verification_status']);
        });
    }
};
