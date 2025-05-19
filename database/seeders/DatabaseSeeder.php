<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    private readonly array $users;
    private readonly array $roles;
    private readonly array $genres;

    function __construct()
    {
        $this->roles = [
            'admin' => 'admin',
            'moderator' => 'moderator',
            'user' => 'user',
        ];

        $this->users = [
            [
                'name' => 'Пользователь',
                'email' => 'user@anymail.ru',
                'password' => '123456',
                'role' => 'user',
            ],
            [
                'name' => 'Администратор',
                'email' => 'admin@anymail.ru',
                'password' => '123456',
                'role' => 'admin',
            ],
        ];

        $this->genres = [
            'Crime' => 'Crime',
            'Thriller' => 'Thriller',
            'Adventure' => 'Adventure',
            'Comedy' => 'Comedy',
            'Drama' => 'Drama',
            'Action' => 'Action',
            'Fantasy' => 'Fantasy',
        ];

    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedRoles();
        $this->seedUsers();
        $this->seedGenres();
    }

    private function seedRoles(): void {
        $count = DB::table('roles')->get()->count();

        if($count <= 0) {
            foreach ($this->roles as $role) {
                DB::table('roles')->insert([
                    'id' => Str::uuid(),
                    'name' => $role,
                ]);
            }
        }
    }

    private function seedUsers(): void {
        $count = DB::table('users')->get()->count();

        if($count <= 0) {
            foreach ($this->users as $user) {
                $role = Role::query()->where('name', '=', $user['role'])->first();

                User::query()->create([
                    "id" => Str::uuid(),
                    "name" => $user['name'],
                    "email" => $user['email'],
                    "password" => $user['password'],
                    "role_id" => $role->id,
                ]);
            }
        }
    }

    private function seedGenres(): void {
        $count = Genre::query()->get()->count();

        if($count <= 0) {
            foreach ($this->genres as $genre) {
                Genre::query()->create([
                    'id' => Str::uuid(),
                    'name' => $genre,
                ]);
            }
        }
    }
}
