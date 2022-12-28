<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        if (DB::table('users')->count() == 0) {

            DB::table('users')->insert([
                [
                    'name' => "Amanmyrat Tekemuradov",
                    'email' => "tekemuradov@gmail.com",
                    'password' => Hash::make('Aman4ik_98'),
                    'remember_token' => Str::random(10),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],

            ]);
        } else {
            echo "\e[31mTable is not empty, therefore NOT ";
        }
    }
}
