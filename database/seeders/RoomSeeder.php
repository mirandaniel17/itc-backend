<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        Room::create(['name' => 'Sala 1']);
        Room::create(['name' => 'Sala 2']);
        Room::create(['name' => 'Sala 3']);
    }
}
