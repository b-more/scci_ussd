<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $seedCompanies = [
            'Seed Co Zambia' => [
                'varieties' => ['SC-513', 'SC-627', 'SC-633', 'SC-719', 'SC-308'],
                'license' => 'SCCI-SCZ-2024-001',
            ],
            'Zamseed' => [
                'varieties' => ['ZMS 606', 'ZMS 528', 'ZMS 402', 'ZMS 638'],
                'license' => 'SCCI-ZS-2024-002',
            ],
            'MRI Seed' => [
                'varieties' => ['MRI 514', 'MRI 624', 'MRI 634', 'MRI 724'],
                'license' => 'SCCI-MRI-2024-003',
            ],
        ];

        $regions = [
            'Lusaka' => ['Chilanga', 'Kafue', 'Chongwe', 'Rufunsa'],
            'Central' => ['Kabwe', 'Mkushi', 'Serenje', 'Kapiri Mposhi'],
            'Copperbelt' => ['Ndola', 'Kitwe', 'Chingola', 'Luanshya'],
            'Eastern' => ['Chipata', 'Katete', 'Petauke', 'Lundazi'],
            'Southern' => ['Choma', 'Mazabuka', 'Monze', 'Kalomo'],
        ];

        $seedTypes = [
            'Maize' => ['quantity' => [5, 10, 20, 50]],
            'Soybean' => ['quantity' => [10, 25, 50]],
            'Groundnuts' => ['quantity' => [5, 10, 20]],
            'Wheat' => ['quantity' => [25, 50]],
            'Sunflower' => ['quantity' => [2, 5, 10]],
        ];

        // Generate 100 vouchers
        for ($i = 1; $i <= 100; $i++) {
            $company = array_rand($seedCompanies);
            $region = array_rand($regions);
            $district = $regions[$region][array_rand($regions[$region])];
            $seedType = array_rand($seedTypes);
            $variety = $seedCompanies[$company]['varieties'][array_rand($seedCompanies[$company]['varieties'])];
            $quantity = $seedTypes[$seedType]['quantity'][array_rand($seedTypes[$seedType]['quantity'])];

            $validFrom = Carbon::now()->subDays(rand(0, 30));
            
            Voucher::create([
                'voucher_number' => 'SCCI-' . date('Y') . sprintf('-%03d', $i),
                'batch_number' => 'BTH-' . date('Y') . sprintf('-%03d', rand(1, 999)),
                'seed_type' => $seedType,
                'seed_variety' => $variety,
                'seed_class' => array_rand(['Certified' => true, 'Basic' => true]),
                'quantity_kg' => $quantity,
                'seed_company_name' => $company,
                'seed_company_license' => $seedCompanies[$company]['license'],
                'production_date' => Carbon::now()->subMonths(rand(3, 6)),
                'testing_date' => Carbon::now()->subMonths(rand(1, 2)),
                'packaging_date' => Carbon::now()->subDays(rand(15, 45)),
                'laboratory_test_number' => 'LAB-' . date('Y') . sprintf('-%04d', rand(1, 9999)),
                'germination_rate' => rand(85, 99),
                'purity_rate' => rand(98, 100),
                'moisture_content' => rand(12, 14),
                'valid_from' => $validFrom,
                'valid_until' => $validFrom->copy()->addMonths(12),
                'is_active' => true,
                'is_used' => rand(0, 1),
                'region' => $region,
                'district' => $district,
                'distribution_point' => 'Agro Dealer ' . rand(1, 5),
                'created_by' => 'System Seeder',
                'approved_by' => 'SCCI Officer ' . rand(1, 5),
                'status' => 'active',
                'comments' => 'Sample voucher data for testing purposes',
            ]);
        }
    }
}