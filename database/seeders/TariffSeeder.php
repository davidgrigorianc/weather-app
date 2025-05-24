<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tariff;

class TariffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tariff::insert([
            ['key' => 'tariff_25', 'price' => 25, 'count' => 10],
            ['key' => 'tariff_50', 'price' => 50, 'count' => 30],
            ['key' => 'tariff_100', 'price' => 100, 'count' => 100],
        ]);
    }
}
