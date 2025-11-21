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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('billing_period'); // monthly, yearly
            $table->json('features'); // Array of features
            $table->integer('max_posts_per_month')->nullable();
            $table->integer('max_platforms')->nullable();
            $table->boolean('ai_content')->default(false);
            $table->boolean('analytics')->default(false);
            $table->boolean('scheduling')->default(true);
            $table->boolean('is_active')->default(true);
            $table->string('stripe_price_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
