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
            // Add USD pricing columns
            $table->decimal('monthly_price_usd', 10, 2)->nullable()->after('yearly_price');
            $table->decimal('yearly_price_usd', 10, 2)->nullable()->after('monthly_price_usd');
            
            // Add INR pricing columns
            $table->decimal('monthly_price_inr', 10, 2)->nullable()->after('yearly_price_usd');
            $table->decimal('yearly_price_inr', 10, 2)->nullable()->after('monthly_price_inr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['monthly_price_usd', 'yearly_price_usd', 'monthly_price_inr', 'yearly_price_inr']);
        });
    }
};
