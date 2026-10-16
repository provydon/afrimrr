<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('startups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('category')->default('other');
            $table->string('country', 8)->default('NG');
            $table->unsignedSmallInteger('founded_year')->nullable();

            // Verification + revenue (normalized to USD cents for ranking)
            $table->boolean('is_verified')->default(false);
            $table->string('verification_source')->nullable(); // paystack|flutterwave|stripe|manual
            $table->unsignedBigInteger('mrr_usd')->default(0);  // USD cents
            $table->unsignedBigInteger('mrr_local')->default(0); // local minor units
            $table->string('display_currency', 8)->default('USD');
            $table->unsignedInteger('active_subscriptions')->default(0);
            $table->decimal('mom_growth', 8, 2)->nullable(); // month-over-month %
            $table->timestamp('last_synced_at')->nullable();

            // Marketplace
            $table->boolean('is_public')->default(true);
            $table->boolean('is_for_sale')->default(false);
            $table->unsignedBigInteger('asking_price_usd')->nullable(); // USD cents

            $table->boolean('is_demo')->default(false);
            $table->timestamps();

            $table->index(['is_public', 'mrr_usd']);
            $table->index('category');
            $table->index('country');
            $table->index('is_for_sale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('startups');
    }
};
