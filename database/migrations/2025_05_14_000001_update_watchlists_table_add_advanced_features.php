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
        Schema::table('watchlists', function (Blueprint $table) {
            // Add category field for organizing content (watch soon, watch later, etc.)
            $table->string('category')->default('default')->after('title_id');
            
            // Add position/order in the list for manual sorting
            $table->integer('position')->default(0)->after('category');
            
            // Add priority field (high, medium, low)
            $table->string('priority')->default('medium')->after('position');
            
            // Add notes field for user annotations
            $table->text('notes')->nullable()->after('priority');
            
            // Add a "liked" field for quick favoriting
            $table->boolean('liked')->default(false)->after('notes');
            
            // Add index on category and position for faster sorting/filtering
            $table->index(['profile_id', 'category', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('watchlists', function (Blueprint $table) {
            // Remove the indexes
            $table->dropIndex(['profile_id', 'category', 'position']);
            
            // Drop the new columns
            $table->dropColumn(['category', 'position', 'priority', 'notes', 'liked']);
        });
    }
};