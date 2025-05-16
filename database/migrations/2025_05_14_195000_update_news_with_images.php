<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\News;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Actualizar todas las noticias que no tienen imagen
        $newsWithoutImages = News::whereNull('image')->orWhere('image', '')->get();
        
        foreach ($newsWithoutImages as $news) {
            $imageNumber = rand(1, 5);
            $news->update([
                'image' => "images/news/news-placeholder-{$imageNumber}.jpg"
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};