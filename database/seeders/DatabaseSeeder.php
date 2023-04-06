<?php

namespace Database\Seeders;

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
        // User::create([
        //     'username' => 'developer',
        //     'password' => bcrypt('secret'),
        //     'name' => 'Developer'
        // ]);
        // \App\Models\User::factory(500)->create();

        $this->call([
            TicketSeeder::class
        ]);
    }
}
