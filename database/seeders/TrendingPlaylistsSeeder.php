<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrendingPlaylist;

class TrendingPlaylistsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $playlists = [
         
            [
                'playlist_url' => 'https://www.youtube.com/playlist?list=PLDcnymzs18LW9H-Lqf6l0Xz0GTb5b8BM8',
                'title' => 'EDM Hits 2021',
                'thumbnail' => 'https://i.ytimg.com/vi/3xgtN653Qvo/hqdefault.jpg',
                'download_count' => 0
            ],
            [
                'playlist_url' => 'https://www.youtube.com/playlist?list=PLiyfwJUpqIlU1Rwn64a1uGecqIJ_Grdr1',
                'title' => 'Hip Hop 2021',
                'thumbnail' => 'https://i.ytimg.com/vi/3xgtN653Qvo/hqdefault.jpg',
                'download_count' => 0
            ],
            [
                'playlist_url' => 'https://www.youtube.com/playlist?list=PLzGLK0V3wEVXkx8D4K9m_vYgbjRJtH1uF',
                'title' => 'Pop Hits 2021',
                'thumbnail' => 'https://i.ytimg.com/vi/3xgtN653Qvo/hqdefault.jpg',
                'download_count' => 0
            ],
            [
                'playlist_url' => 'https://www.youtube.com/playlist?list=PLs4hTtftqnlPtF1mOrSIzbnTW2cY1VGBV',
                'title' => 'Rock Hits 2021',
                'thumbnail' => 'https://i.ytimg.com/vi/3xgtN653Qvo/hqdefault.jpg',
                'download_count' => 0
            ]
            // Add more playlists as needed
        ];

        foreach ($playlists as $playlist) {
            TrendingPlaylist::create($playlist);
        }
    }
}
