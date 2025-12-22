<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_metadata', function (Blueprint $table) {
            $table->id();
            
            // Polymorphic relationship (morphs() already creates an index)
            $table->morphs('seoable');
            
            // Meta Tags
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            
            // Open Graph Tags
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('og_type')->default('website');
            
            // Twitter Card Tags
            $table->string('twitter_card')->default('summary_large_image');
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();
            $table->string('twitter_site')->nullable();
            $table->string('twitter_creator')->nullable();
            
            // Technical SEO
            $table->string('canonical_url')->nullable();
            $table->boolean('index')->default(true);
            $table->boolean('follow')->default(true);
            
            // Structured Data
            $table->json('schema_markup')->nullable();
            
            // Additional
            $table->string('locale')->default('en_US');
            $table->decimal('priority', 2, 1)->default(0.5);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_metadata');
    }
};
