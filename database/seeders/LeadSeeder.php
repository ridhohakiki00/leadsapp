<?php

namespace Database\Seeders;

use App\Models\Lead;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class LeadSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 20) as $i) {
            Lead::create([
                'nomor' => 'UF' . now()->format('ym') . str_pad($i, 4, '0', STR_PAD_LEFT),
                'tanggal' => $faker->dateTimeBetween('-1 year', 'now'),
                'nama' => $faker->name,
                'nohp' => $faker->phoneNumber,
                'alamat' => $faker->address,
                'kelurahan' => $faker->word,
                'kecamatan' => $faker->word,
                'kota' => $faker->city,
                'tipe' => $faker->randomElement(['SUV', 'Sedan', 'Truck']),
                'warna' => $faker->safeColorName,
                'leasing' => $faker->company,
                'tenor' => $faker->randomElement(['12', '24', '36']),
                'tanggal_kredit' => $faker->dateTimeBetween('-1 year', 'now'),
                'asuransi' => $faker->randomElement(['All Risk', 'TLO']),
                'hargajual' => $faker->numberBetween(100_000_000, 300_000_000),
                'discount' => $faker->numberBetween(0, 10_000_000),
                'status' => $faker->randomElement(['Lunas', 'Pending']),
                'distribusi' => $faker->randomElement(['Dealer', 'Langsung']),
                'salesman' => $faker->name,
                'followup' => $faker->randomElement(['Ya', 'Tidak']),
                'statusfollowup' => $faker->randomElement(['Deal', 'Pending', 'Tolak']),
                'tglfollowup' => $faker->dateTimeBetween('-1 year', 'now'),
                'hasilfollowup' => $faker->sentence(3),
            ]);
        }
    }
}
