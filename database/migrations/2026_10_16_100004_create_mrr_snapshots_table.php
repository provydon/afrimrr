<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mrr_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('startup_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('mrr_usd')->default(0);
            $table->unsignedBigInteger('mrr_local')->default(0);
            $table->string('display_currency', 8)->default('USD');
            $table->unsignedInteger('active_subscriptions')->default(0);
            $table->date('captured_on');
            $table->timestamps();

            $table->unique(['startup_id', 'captured_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mrr_snapshots');
    }
};
