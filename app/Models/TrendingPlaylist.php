<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrendingPlaylist extends Model
{
    use HasFactory;

    protected $fillable = [
        'playlist_url', 'title', 'thumbnail', 'download_count',
    ];
}
