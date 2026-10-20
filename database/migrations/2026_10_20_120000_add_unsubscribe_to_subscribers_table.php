<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->string('unsubscribe_token', 64)->nullable()->unique()->after('source');
            $table->timestamp('unsubscribed_at')->nullable()->after('unsubscribe_token');
            $table->timestamp('last_sent_at')->nullable()->after('unsubscribed_at');
        });
    }

    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropColumn(['unsubscribe_token', 'unsubscribed_at', 'last_sent_at']);
        });
    }
};
