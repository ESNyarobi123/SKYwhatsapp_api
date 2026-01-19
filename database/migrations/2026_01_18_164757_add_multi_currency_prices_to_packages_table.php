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
            // Add multi-currency price columns
            $table->decimal('price_tsz', 10, 2)->nullable()->after('price');
            $table->decimal('price_usd', 10, 2)->nullable()->after('price_tsz');
            $table->decimal('price_usdt', 10, 2)->nullable()->after('price_usd');
            
            // Change default currency from TZS to USD
        });

        // Update existing records: migrate current price to price_usd (default currency)
        \DB::statement("UPDATE packages SET price_usd = price WHERE price_usd IS NULL");
        
        // Update currency default to USD in database
        \DB::statement("ALTER TABLE packages MODIFY COLUMN currency VARCHAR(3) NOT NULL DEFAULT 'USD'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['price_tsz', 'price_usd', 'price_usdt']);
        });
        
        // Revert currency default to TZS
        \DB::statement("ALTER TABLE packages MODIFY COLUMN currency VARCHAR(3) NOT NULL DEFAULT 'TZS'");
    }
};
