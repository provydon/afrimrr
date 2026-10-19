<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('startups', function (Blueprint $table) {
            // Listed and ranked by MRR, but identity (name/logo/profile) hidden.
            $table->boolean('is_anonymous')->default(false)->after('is_public');
        });
    }

    public function down(): void
    {
        Schema::table('startups', function (Blueprint $table) {
            $table->dropColumn('is_anonymous');
        });
    }
};
