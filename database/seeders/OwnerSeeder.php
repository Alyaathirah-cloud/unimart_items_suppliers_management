<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Owner Admin',
            'email' => 'owner@unimart.com',
            'password' => bcrypt('password'),
            'role' => 'owner',
        ]);
    }
}
