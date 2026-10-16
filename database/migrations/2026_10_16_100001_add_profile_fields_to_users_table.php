<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('headline')->nullable()->after('username');
            $table->text('bio')->nullable()->after('headline');
            $table->string('avatar_url')->nullable()->after('bio');
            $table->string('twitter')->nullable()->after('avatar_url');
            $table->string('website')->nullable()->after('twitter');
            $table->boolean('is_admin')->default(false)->after('website');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'headline', 'bio', 'avatar_url', 'twitter', 'website', 'is_admin']);
        });
    }
};
