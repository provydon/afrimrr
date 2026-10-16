<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('startup_id')->constrained()->cascadeOnDelete();
            $table->string('provider'); // paystack|flutterwave|stripe
            $table->text('credentials'); // encrypted secret key
            $table->string('status')->default('pending'); // pending|active|error
            $table->text('last_error')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->unique(['startup_id', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('connections');
    }
};
