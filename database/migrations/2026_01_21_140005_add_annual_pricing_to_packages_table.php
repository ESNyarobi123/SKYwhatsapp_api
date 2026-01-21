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
        Schema::table('packages', function (Blueprint $table) {
            // Annual pricing with discount
            $table->decimal('price_usd_annual', 10, 2)->nullable()->after('price_usdt');
            $table->decimal('price_tsz_annual', 10, 2)->nullable()->after('price_usd_annual');
            $table->decimal('price_usdt_annual', 10, 2)->nullable()->after('price_tsz_annual');
            $table->integer('annual_discount_percent')->default(20)->after('price_usdt_annual'); // Default 20% discount
            
            // For popular badge and comparison
            $table->boolean('is_popular')->default(false)->after('is_active');
            $table->json('feature_tooltips')->nullable()->after('features'); // Tooltips for feature descriptions
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn([
                'price_usd_annual',
                'price_tsz_annual', 
                'price_usdt_annual',
                'annual_discount_percent',
                'is_popular',
                'feature_tooltips'
            ]);
        });
    }
};
