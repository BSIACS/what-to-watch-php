<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promo extends Model
{
    use HasFactory, HasUuids;


    protected $table = 'promo';

    protected $fillable = [
        'id',
        'film_id',
    ];

    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }
}
