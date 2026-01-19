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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('instance_id')->constrained()->cascadeOnDelete();
            $table->string('message_id'); // external message ID
            $table->enum('direction', ['inbound', 'outbound']);
            $table->enum('status', ['sent', 'delivered', 'read', 'failed'])->default('sent');
            $table->string('to');
            $table->string('from');
            $table->text('body');
            $table->json('metadata')->nullable();
            $table->timestamp('created_at');

            $table->index(['user_id', 'instance_id']);
            $table->index(['user_id', 'direction', 'created_at']);
            $table->index('message_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
