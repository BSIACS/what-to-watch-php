<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FilmStatus extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'film_statuses';

    public function users(): HasMany {
        return $this->hasMany(Film::class);
    }
}
