<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Film;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Film>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Comment::class;

    public function definition(): array
    {
        $film = Film::query()->inRandomOrder()->first();
        $user = User::query()->inRandomOrder()->first();

        return [
            'id' => Str::uuid(),
            'text' => fake()->realText(mt_rand(50, 400)),
            'film_id' => $film->id,
            'user_id' => mt_rand(1, 7) === 1 ? null : $user->id,
        ];
    }
}
