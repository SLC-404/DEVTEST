<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'SuperAdministrador',
                'password' => 'SVLgYyxTiy.6',
            ]
        );

        $admin->syncRoles('SuperAdministrador');
    }
}
