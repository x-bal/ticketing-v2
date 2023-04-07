<?php

namespace Database\Seeders;

use App\Models\Ticket;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tickets = [
            ["name" =>  "HTM Reguler Weekday", "harga" => 20000],
            ["name" =>  "HTM Reguler Weekend", "harga" => 30000],
            ["name" =>  "HTM Terusan Weekday", "harga" => 40000],
            ["name" =>  "HTM Terusan Weekend", "harga" => 50000],
            ["name" =>  "HTM Group Character Building", "harga" => 70000],
            ["name" =>  "HTM Olahraga Sekolah", "harga" => 15000],
            ["name" =>  "HTM Club Renang", "harga" => 15000],
            ["name" =>  "Tiket Balon Udara", "harga" => 10000],
            ["name" =>  "Tiket Adrenaline Swing", "harga" => 10000],
            ["name" =>  "Tiket Sepeda Terbang", "harga" => 10000],
            ["name" =>  "HTM Rainbow Slider", "harga" => 35000],
            ["name" =>  "HTM Kereta", "harga" => 25000],
            ["name" =>  "HTM Adrenaline Slider Air Terjun", "harga" => 25000],
        ];

        foreach ($tickets as $ticket) {
            Ticket::create([
                'name' => $ticket['name'],
                'harga' => $ticket['harga'],
            ]);
        }
    }
}
