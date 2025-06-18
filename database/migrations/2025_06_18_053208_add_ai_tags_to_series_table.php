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
        Schema::table('series', function (Blueprint $table) {
            $table->text('ai_tags')->nullable()->after('status');
            $table->timestamp('ai_tags_generated_at')->nullable()->after('ai_tags');
            
            // Index for searching by tags
            $table->index(['ai_tags_generated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('series', function (Blueprint $table) {
            $table->dropIndex(['ai_tags_generated_at']);
            $table->dropColumn(['ai_tags', 'ai_tags_generated_at']);
        });
    }
};