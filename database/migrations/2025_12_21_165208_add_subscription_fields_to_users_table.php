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
        Schema::table('users', function (Blueprint $table) {
            // Currency tracking
            $table->string('currency', 3)->default('USD')->after('email');
            
            // Plan relationship
            $table->foreignId('plan_id')->nullable()->after('currency')->constrained('plans')->nullOnDelete();
            $table->timestamp('plan_started_at')->nullable()->after('plan_id');
            
            // Usage tracking
            $table->integer('resumes_created')->default(0)->after('plan_started_at');
            $table->integer('ats_scans_used')->default(0)->after('resumes_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn(['currency', 'plan_id', 'plan_started_at', 'resumes_created', 'ats_scans_used']);
        });
    }
};
