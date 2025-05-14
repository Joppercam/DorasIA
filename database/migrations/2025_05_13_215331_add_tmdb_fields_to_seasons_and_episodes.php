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
        // Add TMDB fields to seasons table
        Schema::table('seasons', function (Blueprint $table) {
            $table->unsignedBigInteger('tmdb_id')->nullable()->after('id');
            
            // Add overview only if it doesn't exist
            if (!Schema::hasColumn('seasons', 'overview')) {
                $table->text('overview')->nullable()->after('title');
            }
            
            // Add air_date only if it doesn't exist
            if (!Schema::hasColumn('seasons', 'air_date')) {
                $table->date('air_date')->nullable()->after('title');
            }
            
            // Add episode_count only if it doesn't exist
            if (!Schema::hasColumn('seasons', 'episode_count')) {
                $table->unsignedInteger('episode_count')->nullable()->after('air_date');
            }
            
            // Add index for faster lookups
            $table->index('tmdb_id');
        });
        
        // Add TMDB fields to episodes table
        Schema::table('episodes', function (Blueprint $table) {
            $table->unsignedBigInteger('tmdb_id')->nullable()->after('id');
            
            // Change synopsis to text if it exists
            if (Schema::hasColumn('episodes', 'synopsis')) {
                $table->text('synopsis')->nullable()->change();
            }
            
            // Add air_date only if it doesn't exist
            if (!Schema::hasColumn('episodes', 'air_date')) {
                $table->date('air_date')->nullable()->after('synopsis');
            }
            
            // Add thumbnail only if it doesn't exist
            if (!Schema::hasColumn('episodes', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('duration');
            }
            
            // Add index for faster lookups
            $table->index('tmdb_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove TMDB fields from seasons table
        Schema::table('seasons', function (Blueprint $table) {
            $table->dropIndex(['tmdb_id']);
            $table->dropColumn([
                'tmdb_id',
                'overview',
                'air_date',
                'episode_count',
            ]);
        });
        
        // Remove TMDB fields from episodes table
        Schema::table('episodes', function (Blueprint $table) {
            $table->dropIndex(['tmdb_id']);
            $table->dropColumn([
                'tmdb_id',
                'air_date',
                'thumbnail',
            ]);
            
            // Revert synopsis back to string but this may lose data if done in production
            $table->string('synopsis')->change();
        });
    }
};