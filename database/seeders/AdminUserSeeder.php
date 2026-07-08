<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        AdminUser::firstOrCreate(
            ['email' => env('ADMIN_INITIAL_EMAIL', 'admin@example.com')],
            [
                'name'      => '管理者',
                'password'  => Hash::make(env('ADMIN_INITIAL_PASSWORD', 'changeme')),
                'is_active' => true,
            ]
        );
    }
}
