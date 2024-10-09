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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->enum('language', ['en', 'ru', 'lt']);
            $table->string('key');
            $table->text('value');
            $table->timestamps();

            // You may want to add unique constraints or indexes if necessary
            // For example, to ensure unique keys per language:
            $table->unique(['language', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
