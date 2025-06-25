<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            'site_name' => 'Bamboo Sông Chanh',
            'site_address' => 'TP Hà Nội',
            'site_phone' => '0123456789',
            'site_email' => 'info@bamboosongchanh.vn',
            'site_description' => 'Nhà hàng món Việt truyền thống',
            'opening_hours' => '07:00 - 22:00',
            'facebook_url' => '',
            'instagram_url' => '',
            'youtube_url' => '',
            'currency' => 'VND',
            'logo_url' => '',
            'banner_url' => '',
            'tax_rate' => '10',
            'service_charge' => '5',
        ];

        foreach ($defaultSettings as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
