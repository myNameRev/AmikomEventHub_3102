<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Partner;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partners = [
            [
                'name' => 'Tech Innovation Labs',
                'logo_url' => 'https://placehold.co/200x200?text=Tech+Innovation'
            ],
            [
                'name' => 'Digital Solutions Inc',
                'logo_url' => 'https://placehold.co/200x200?text=Digital+Solutions'
            ],
            [
                'name' => 'Creative Studios Co',
                'logo_url' => 'https://placehold.co/200x200?text=Creative+Studios'
            ],
            [
                'name' => 'Global Ventures Partners',
                'logo_url' => 'https://placehold.co/200x200?text=Global+Ventures'
            ],
            [
                'name' => 'Enterprise Solutions Group',
                'logo_url' => 'https://placehold.co/200x200?text=Enterprise+Solutions'
            ]
        ];

        foreach ($partners as $partner) {
            Partner::create($partner);
        }
    }
}
