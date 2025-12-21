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
        Schema::create('resume_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable(); // For guest users
            $table->enum('input_type', ['upload', 'paste'])->default('upload');
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->integer('file_size')->nullable();
            $table->text('pasted_content')->nullable();
            $table->integer('ats_score')->default(0);
            $table->string('score_grade')->nullable(); // 'excellent', 'good', 'needs_improvement', 'poor'
            $table->json('matched_keywords')->nullable();
            $table->json('missing_keywords')->nullable();
            $table->json('formatting_checks')->nullable();
            $table->json('content_analysis')->nullable();
            $table->json('critical_issues')->nullable();
            $table->string('experience_level')->nullable();
            $table->integer('word_count')->default(0);
            $table->integer('keyword_match_percentage')->default(0);
            $table->text('improvement_suggestions')->nullable();
            $table->text('raw_ai_response')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resume_analyses');
    }
};
