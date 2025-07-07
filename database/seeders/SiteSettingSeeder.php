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
            'site_name' => 'Nhà Hàng Bamboo Sông Chanh',
            'site_tagline' => 'Tinh hoa ẩm thực Quảng Yên',
            'contact_email' => 'info@bamboosongchanh.vn',
            'contact_phone' => '0901234567',
            'address' => '123 Đường Lê Lợi, Quận 1, TP. Hồ Chí Minh',
            'opening_hours' => '08:00 - 22:00 (Thứ 2 - Chủ nhật)',
            'facebook_url' => 'https://facebook.com/restaurant',
            'zalo_url' => 'https://zalo.com/restaurant',
            'primary_color' => '#e53935',
            'secondary_color' => '#4caf50',
            'accent_color' => '#ff9800',
            'heading_font' => 'Montserrat, sans-serif',
            'body_font' => 'Roboto, sans-serif',
            'font_size' => 'medium',
            'logo' => '[]',
            'favicon' => '[]',
            'banner_images' => '[]',
        ];

        foreach ($defaultSettings as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
