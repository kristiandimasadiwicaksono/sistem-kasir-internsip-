<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Admin Utama',
            'email' => 'kristiandimasadiwicaksono@gmail.com',
            'password' => Hash::make('AdminKasir123'),
            'role' => 'admin',
        ]);
    }
}
