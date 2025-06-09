<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Film extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'name',
        'poster_image',
        'preview_image',
        'background_image',
        'background_color',
        'released',
        'description',
        'director',
        'starring',
        'run_time',
        'video_link',
        'preview_video_link',
        'imdb_id',
        'status_id',
        'rating',
        'score_count',
    ];

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'film_genre');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'film_user');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(FilmStatus::class);
    }

    public function comments(): HasMany {
        return $this->hasMany(Comment::class);
    }
}
