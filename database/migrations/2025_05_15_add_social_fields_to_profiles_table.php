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
        Schema::table('profiles', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('avatar');
            $table->string('location')->nullable()->after('bio');
            $table->json('favorite_genres')->nullable()->after('location');
            $table->boolean('is_public')->default(true)->after('favorite_genres');
            $table->boolean('allow_messages')->default(true)->after('is_public');
            $table->integer('followers_count')->default(0)->after('allow_messages');
            $table->integer('following_count')->default(0)->after('followers_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn([
                'bio',
                'location',
                'favorite_genres',
                'is_public',
                'allow_messages',
                'followers_count',
                'following_count'
            ]);
        });
    }
};