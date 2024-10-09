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

            // Foreign key to the conversation (cascade delete if conversation is deleted)
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');

            // Foreign keys for sender and receiver (nullable to handle cases where a user is deleted)
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('receiver_id')->nullable()->constrained('users')->nullOnDelete();

            // Message content
            $table->text('body')->nullable(); // Nullable to allow file-only messages

            // File-related fields
            $table->string('file_path')->nullable(); // Path to the uploaded file
            $table->string('file_name')->nullable(); // Original file name for reference
            $table->string('file_type')->nullable(); // MIME type of the file

            // Message deletion timestamps for both sender and receiver
            $table->timestamp('receiver_deleted_at')->nullable();
            $table->timestamp('sender_deleted_at')->nullable();

            // Message read status
            $table->timestamp('read_at')->nullable();

            // Soft deletes to allow single message deletion
            $table->softDeletes();

            // Timestamps for message creation and updates
            $table->timestamps();
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
