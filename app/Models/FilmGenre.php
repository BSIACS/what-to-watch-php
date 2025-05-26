<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FilmGenre extends Model
{
//    use HasFactory, HasUuids;
    protected $table = 'film_genre';
    protected $fillable = [
        'film_id',
        'genre_id',
    ];

}
