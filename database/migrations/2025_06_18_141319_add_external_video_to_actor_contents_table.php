<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('actor_contents', function (Blueprint $table) {
            $table->string('external_video_url')->nullable()->after('external_url');
            $table->enum('external_video_type', ['tiktok', 'youtube', 'instagram', 'other'])->nullable()->after('external_video_url');
        });
    }

    public function down()
    {
        Schema::table('actor_contents', function (Blueprint $table) {
            $table->dropColumn(['external_video_url', 'external_video_type']);
        });
    }
};