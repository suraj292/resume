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
            // Add currency_code to distinguish USD vs INR plans
            $table->string('currency_code', 3)->default('USD')->after('currency');
            
            // Add index for faster filtering
            $table->index('currency_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropIndex(['currency_code']);
            $table->dropColumn('currency_code');
        });
    }
};
