<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Genre extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'name',
    ];

    public function films(): BelongsToMany
    {
        return $this->belongsToMany(Film::class, 'film_genre');
    }
}
