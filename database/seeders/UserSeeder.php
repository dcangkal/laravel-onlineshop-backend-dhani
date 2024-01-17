<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::factory(9)->create();

        $user = \App\Models\User::factory()->create([
            'name' => 'Rahmat Ramadhani',
            'email' => 'dhani@fic12.com',
            'password' => Hash::make('18273645'),
            'phone' => '08119999999',
            'roles' => 'ADMIN',
        ]);
    }
}
