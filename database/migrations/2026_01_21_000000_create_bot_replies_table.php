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
        Schema::create('bot_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instance_id')->constrained()->cascadeOnDelete();
            $table->string('keyword');
            $table->enum('match_type', ['exact', 'contains'])->default('exact');
            $table->enum('reply_type', ['text'])->default('text'); // Can expand to 'media' later
            $table->text('reply_content');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Unique constraint: A keyword should be unique per instance (optional, but good for exact matches)
            // For 'contains', it might be tricky, but let's enforce uniqueness for now to avoid conflicts
            // actually, let's not enforce unique on DB level for flexibility, but maybe validation level.
            $table->index(['instance_id', 'keyword']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bot_replies');
    }
};
