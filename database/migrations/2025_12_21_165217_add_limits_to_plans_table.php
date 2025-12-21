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
        Schema::table('plans', function (Blueprint $table) {
            // Usage limits (-1 = unlimited)
            $table->integer('resume_limit')->default(1)->after('sort_order');
            $table->integer('ats_scan_limit')->default(3)->after('resume_limit');
            
            // Feature flags
            $table->boolean('ai_optimization')->default(false)->after('ats_scan_limit');
            $table->boolean('cover_letter')->default(false)->after('ai_optimization');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['resume_limit', 'ats_scan_limit', 'ai_optimization', 'cover_letter']);
        });
    }
};
