<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RolesSeeder extends Seeder
{
    private readonly array $roles;
    function __construct()
    {
        $this->roles = [
            'admin' => 'admin',
            'moderator' => 'moderator',
            'user' => 'user',
        ];
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedRoles();
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
}
