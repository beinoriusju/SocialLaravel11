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
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->uuid("uuid");
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();

            // New Fields
            $table->string("title", 255);
            $table->longText("description", 500)->nullable();
            $table->longText("details")->nullable();
            $table->foreignId("category_id")->constrained('blog_categories')->cascadeOnDelete();
            $table->foreignId("subcategory_id")->nullable()->constrained('blog_subcategories')->cascadeOnDelete();
            $table->boolean("is_public")->default(true);

            // Existing Fields
            // $table->longText("content")->nullable(); // Remove if not needed
            $table->enum("status", ["published", "pending", "rejected"])->default("pending");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
