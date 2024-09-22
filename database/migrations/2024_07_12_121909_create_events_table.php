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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->uuid("uuid")->unique();
            $table->foreignId("user_id")->constrained()->cascadeOnDelete(); // Link to users table
            $table->string("title", 255); // Event title
            $table->longText("description")->nullable(); // Short description
            $table->longText("details")->nullable(); // Full details
            $table->date('event_date'); // Date of the event
            $table->foreignId("category_id")->constrained('event_categories')->cascadeOnDelete(); // Link to event categories
            $table->foreignId("subcategory_id")->nullable()->constrained('event_subcategories')->cascadeOnDelete(); // Link to event subcategories
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
