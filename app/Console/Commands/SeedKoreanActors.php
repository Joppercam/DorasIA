<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Person;

class SeedKoreanActors extends Command
{
    protected $signature = 'seed:korean-actors';
    protected $description = 'Seed Korean actors for news associations';

    public function handle()
    {
        $actors = [
            [
                'name' => 'Jun Ji-hyun',
                'profile_path' => '/posters/jun-ji-hyun.jpg',
                'known_for_department' => 'Acting',
                'biography' => 'Jun Ji-hyun, also known as Gianna Jun, is a South Korean actress and model.',
                'place_of_birth' => 'Seoul, South Korea',
                'slug' => 'jun-ji-hyun',
                'tmdb_id' => 1062163
            ],
            [
                'name' => 'Lee Min-ho',
                'profile_path' => '/posters/lee-min-ho.jpg',
                'known_for_department' => 'Acting',
                'biography' => 'Lee Min-ho is a South Korean actor, singer, and model.',
                'place_of_birth' => 'Seoul, South Korea',
                'slug' => 'lee-min-ho',
                'tmdb_id' => 86831
            ],
            [
                'name' => 'Bae Suzy',
                'profile_path' => '/posters/bae-suzy.jpg',
                'known_for_department' => 'Acting',
                'biography' => 'Bae Su-ji, better known as Suzy, is a South Korean singer and actress.',
                'place_of_birth' => 'Gwangju, South Korea',
                'slug' => 'bae-suzy',
                'tmdb_id' => 1273831
            ],
            [
                'name' => 'Hyun Bin',
                'profile_path' => '/posters/hyun-bin.jpg',
                'known_for_department' => 'Acting',
                'biography' => 'Hyun Bin is a South Korean actor.',
                'place_of_birth' => 'Seoul, South Korea',
                'slug' => 'hyun-bin',
                'tmdb_id' => 85089
            ],
            [
                'name' => 'Son Ye-jin',
                'profile_path' => '/posters/son-ye-jin.jpg',
                'known_for_department' => 'Acting',
                'biography' => 'Son Ye-jin is a South Korean actress.',
                'place_of_birth' => 'Daegu, South Korea',
                'slug' => 'son-ye-jin',
                'tmdb_id' => 96657
            ],
            [
                'name' => 'Park Bo-gum',
                'profile_path' => '/posters/park-bo-gum.jpg',
                'known_for_department' => 'Acting',
                'biography' => 'Park Bo-gum is a South Korean actor and singer.',
                'place_of_birth' => 'Seoul, South Korea',
                'slug' => 'park-bo-gum',
                'tmdb_id' => 1592701
            ],
            [
                'name' => 'Kim Soo-hyun',
                'profile_path' => '/posters/kim-soo-hyun.jpg',
                'known_for_department' => 'Acting',
                'biography' => 'Kim Soo-hyun is a South Korean actor.',
                'place_of_birth' => 'Seoul, South Korea',
                'slug' => 'kim-soo-hyun',
                'tmdb_id' => 146446
            ],
            [
                'name' => 'Song Hye-kyo',
                'profile_path' => '/posters/song-hye-kyo.jpg',
                'known_for_department' => 'Acting',
                'biography' => 'Song Hye-kyo is a South Korean actress.',
                'place_of_birth' => 'Daegu, South Korea',
                'slug' => 'song-hye-kyo',
                'tmdb_id' => 66961
            ],
            [
                'name' => 'Park Shin-hye',
                'profile_path' => '/posters/park-shin-hye.jpg',
                'known_for_department' => 'Acting',
                'biography' => 'Park Shin-hye is a South Korean actress and singer.',
                'place_of_birth' => 'Gwangju, South Korea',
                'slug' => 'park-shin-hye',
                'tmdb_id' => 138653
            ],
            [
                'name' => 'Lee Jong-suk',
                'profile_path' => '/posters/lee-jong-suk.jpg',
                'known_for_department' => 'Acting',
                'biography' => 'Lee Jong-suk is a South Korean actor and model.',
                'place_of_birth' => 'Suwon, South Korea',
                'slug' => 'lee-jong-suk',
                'tmdb_id' => 488988
            ],
            [
                'name' => 'Han Hyo-joo',
                'profile_path' => '/posters/han-hyo-joo.jpg',
                'known_for_department' => 'Acting',
                'biography' => 'Han Hyo-joo is a South Korean actress.',
                'place_of_birth' => 'Cheongju, South Korea',
                'slug' => 'han-hyo-joo',
                'tmdb_id' => 85656
            ],
            [
                'name' => 'Lee Jung-jae',
                'profile_path' => '/posters/lee-jung-jae.jpg',
                'known_for_department' => 'Acting',
                'biography' => 'Lee Jung-jae is a South Korean actor and model.',
                'place_of_birth' => 'Seoul, South Korea',
                'slug' => 'lee-jung-jae',
                'tmdb_id' => 19292
            ],
            [
                'name' => 'Park Hae-soo',
                'profile_path' => '/posters/park-hae-soo.jpg',
                'known_for_department' => 'Acting',
                'biography' => 'Park Hae-soo is a South Korean actor.',
                'place_of_birth' => 'Suwon, South Korea',
                'slug' => 'park-hae-soo',
                'tmdb_id' => 1694868
            ],
            [
                'name' => 'Cha Eun-woo',
                'profile_path' => '/posters/cha-eun-woo.jpg',
                'known_for_department' => 'Acting',
                'biography' => 'Lee Dong-min, professionally known as Cha Eun-woo, is a South Korean singer, actor, and model.',
                'place_of_birth' => 'Seoul, South Korea',
                'slug' => 'cha-eun-woo',
                'tmdb_id' => 2297802
            ],
            [
                'name' => 'Kim Tae-ri',
                'profile_path' => '/posters/kim-tae-ri.jpg',
                'known_for_department' => 'Acting',
                'biography' => 'Kim Tae-ri is a South Korean actress.',
                'place_of_birth' => 'Seoul, South Korea',
                'slug' => 'kim-tae-ri',
                'tmdb_id' => 1653348
            ],
            [
                'name' => 'Nam Joo-hyuk',
                'profile_path' => '/posters/nam-joo-hyuk.jpg',
                'known_for_department' => 'Acting',
                'biography' => 'Nam Joo-hyuk is a South Korean model and actor.',
                'place_of_birth' => 'Busan, South Korea',
                'slug' => 'nam-joo-hyuk',
                'tmdb_id' => 1447404
            ],
            [
                'name' => 'Yoo Jae-suk',
                'profile_path' => '/posters/yoo-jae-suk.jpg',
                'known_for_department' => 'Acting',
                'biography' => 'Yoo Jae-suk is a South Korean comedian, MC and television personality.',
                'place_of_birth' => 'Seoul, South Korea',
                'slug' => 'yoo-jae-suk',
                'tmdb_id' => 1179425
            ],
            [
                'name' => 'Jung Ho-yeon',
                'profile_path' => '/posters/jung-ho-yeon.jpg',
                'known_for_department' => 'Acting',
                'biography' => 'Jung Ho-yeon is a South Korean fashion model and actress.',
                'place_of_birth' => 'Seoul, South Korea',
                'slug' => 'jung-ho-yeon',
                'tmdb_id' => 3051859
            ]
        ];

        foreach ($actors as $actorData) {
            Person::updateOrCreate(
                ['slug' => $actorData['slug']],
                $actorData
            );
            $this->info("Created/Updated actor: {$actorData['name']}");
        }

        $this->info('Korean actors seeded successfully!');
    }
}