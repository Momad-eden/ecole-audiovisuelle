<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'file_path',
        'youtube_url',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Extrait l'identifiant de la vidéo YouTube.
     */
    public function getYoutubeIdAttribute(): ?string
    {
        if (!$this->youtube_url) {
            return null;
        }

        $url = trim($this->youtube_url);

        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Récupère la vignette haute résolution de la vidéo YouTube.
     */
    public function getYoutubeThumbnailAttribute(): ?string
    {
        $id = $this->youtube_id;
        return $id ? "https://img.youtube.com/vi/{$id}/hqdefault.jpg" : null;
    }

    /**
     * Récupère l'URL d'intégration (embed) de la vidéo YouTube.
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        $id = $this->youtube_id;
        return $id ? "https://www.youtube-nocookie.com/embed/{$id}?autoplay=1&rel=0" : null;
    }

    /**
     * Récupère l'URL du média ou de sa vignette.
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->type === 'image' && $this->file_path) {
            return asset('storage/' . $this->file_path);
        }

        if ($this->type === 'video' && $this->youtube_thumbnail) {
            return $this->youtube_thumbnail;
        }

        return null;
    }
}