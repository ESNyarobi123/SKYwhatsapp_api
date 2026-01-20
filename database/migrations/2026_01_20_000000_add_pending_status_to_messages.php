<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to modify the enum column as it's the most reliable way for MySQL
        DB::statement("ALTER TABLE messages MODIFY COLUMN status ENUM('pending', 'sent', 'delivered', 'read', 'failed') NOT NULL DEFAULT 'pending'");
        
        // Update existing 'sent' messages to 'pending' if needed? 
        // No, let's keep old history as is. Only new messages will be pending.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum
        // We might have data with 'pending' status now, so we should probably handle that.
        // For now, we'll just revert the column definition.
        DB::statement("UPDATE messages SET status = 'sent' WHERE status = 'pending'");
        DB::statement("ALTER TABLE messages MODIFY COLUMN status ENUM('sent', 'delivered', 'read', 'failed') NOT NULL DEFAULT 'sent'");
    }
};
