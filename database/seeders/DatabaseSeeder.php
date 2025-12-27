<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class DatabaseSeeder extends Seeder
{
    public function run() : void
    {
        Admin::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'username' => 'admin',  // Ensure this column exists in `admins` table
                'password' => Hash::make('admin'),
            ]
        );
    }
}
