<?php

namespace Database\Seeders;

use App\Models\Auctions;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'email'=> 'user@gmail.com',
        ]);
        User::factory()->create([
            'email'=> 'test@gmail.com',
        ]);
        User::factory(10)->create();

        Auctions::factory(20)->create();
    }
}
