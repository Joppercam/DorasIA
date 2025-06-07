<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // Add polymorphic columns
            $table->string('commentable_type')->nullable()->after('user_id');
            $table->unsignedBigInteger('commentable_id')->nullable()->after('commentable_type');
            
            // Add index for polymorphic relationship
            $table->index(['commentable_type', 'commentable_id']);
        });
        
        // Migrate existing series comments to polymorphic structure
        DB::statement("UPDATE comments SET commentable_type = 'App\\Models\\Series', commentable_id = series_id WHERE series_id IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['commentable_type', 'commentable_id']);
            $table->dropColumn(['commentable_type', 'commentable_id']);
        });
    }
};
