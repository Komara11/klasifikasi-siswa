<?php

namespace Database\Seeders;

use App\Models\House;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed default user if not exists
        if (!User::where('email', 'admin@prediksirumah.com')->exists()) {
            User::create([
                'name' => 'Deka (Admin)',
                'username' => 'admin',
                'email' => 'admin@prediksirumah.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]);
        }

        // Default public user
        if (!User::where('email', 'user@example.com')->exists()) {
            User::create([
                'name' => 'Pengguna Umum',
                'username' => 'user',
                'email' => 'user@example.com',
                'password' => Hash::make('user123'),
                'role' => 'user',
            ]);
        }

        // 2. Clear old houses
        House::truncate();

        // 3. Seed Majalengka houses
        $kecamatanData = [
            'Majalengka' => ['multiplier' => 1.35, 'base_dist' => 1.0],
            'Kadipaten' => ['multiplier' => 1.20, 'base_dist' => 12.0],
            'Jatiwangi' => ['multiplier' => 1.15, 'base_dist' => 16.0],
            'Cigasong' => ['multiplier' => 1.10, 'base_dist' => 3.5],
            'Kertajati' => ['multiplier' => 1.05, 'base_dist' => 24.0],
            'Rajagaluh' => ['multiplier' => 1.00, 'base_dist' => 18.0],
            'Dawuan' => ['multiplier' => 0.95, 'base_dist' => 10.0],
            'Talaga' => ['multiplier' => 0.85, 'base_dist' => 28.0],
            'Ligung' => ['multiplier' => 0.80, 'base_dist' => 22.0],
        ];

        $houseTemplates = [
            // [LT, LB, KT, KM, Kondisi, Garasi, Taman, Carport]
            [60, 36, 2, 1, 3, false, true, true],
            [72, 45, 2, 1, 3, false, true, true],
            [90, 54, 3, 1, 4, true, true, true],
            [120, 70, 3, 2, 4, true, true, true],
            [150, 90, 3, 2, 4, true, true, true],
            [200, 120, 4, 2, 5, true, true, true],
            [250, 150, 4, 3, 5, true, true, true],
            [300, 220, 5, 4, 5, true, true, true],
            [84, 60, 2, 2, 3, true, false, true],
            [105, 80, 3, 2, 4, true, true, true],
        ];

        $count = 0;
        foreach ($kecamatanData as $kecamatan => $meta) {
            foreach ($houseTemplates as $index => $t) {
                // Add some random variations
                $lt = $t[0] + rand(-5, 10);
                $lb = $t[1] + rand(-3, 5);
                if ($lb > $lt) $lb = $lt - 10;
                
                $kt = $t[2];
                $km = $t[3];
                $kondisi = rand(2, 5);
                $garasi = $t[5] ? (rand(0, 10) > 2) : (rand(0, 10) > 8);
                $taman = $t[6] ? (rand(0, 10) > 2) : (rand(0, 10) > 7);
                $carport = $t[7] ? (rand(0, 10) > 1) : (rand(0, 10) > 6);
                
                // Jarak kota base distance + random variation
                $jarak = max(0.5, $meta['base_dist'] + rand(-20, 30) / 10.0);

                // Calculate base price
                $basePrice = ($lb * 3800000) + ($lt * 1400000);
                
                // Add factors
                $conditionMult = 0.75 + ($kondisi * 0.08);
                $locMult = $meta['multiplier'];
                
                // Distance penalty: -1.5% per km
                $distMult = max(0.7, 1.0 - ($jarak * 0.015));

                $amenitiesValue = ($garasi ? 15000000 : 0) + ($taman ? 10000000 : 0) + ($carport ? 8000000 : 0);

                $price = ($basePrice * $conditionMult * $locMult * $distMult) + $amenitiesValue;
                
                // Apply final slight randomness (+/- 5%)
                $price = $price * (1 + (rand(-5, 5) / 100.0));

                // Round price to nearest million or 500k
                $price = round($price / 500000) * 500000;

                House::create([
                    'luas_tanah' => $lt,
                    'luas_bangunan' => $lb,
                    'kamar_tidur' => $kt,
                    'kamar_mandi' => $km,
                    'kecamatan' => $kecamatan,
                    'jarak_kota' => $jarak,
                    'kondisi' => $kondisi,
                    'garasi' => $garasi,
                    'taman' => $taman,
                    'carport' => $carport,
                    'harga_aktual' => $price,
                ]);

                $count++;
                if ($count >= 50) break 2;
            }
        }
    }
}
