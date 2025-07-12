<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteInfoController extends Controller
{
    /**
     * Lấy tên site từ database
     */
    public function getSiteName(): JsonResponse
    {
        $siteName = SiteSetting::get('site_name', 'Default Restaurant Name');

        return response()->json([
            'success' => true,
            'site_name' => $siteName
        ]);
    }

    /**
     * Lấy thông tin cơ bản của site
     */
    public function getSiteInfo(): JsonResponse
    {
        $siteInfo = [
            'site_name' => SiteSetting::get('site_name', 'Restaurant Name'),
            'site_description' => SiteSetting::get('site_description', 'Restaurant Description'),
            'contact_phone' => SiteSetting::get('contact_phone', ''),
            'contact_email' => SiteSetting::get('contact_email', ''),
            'address' => SiteSetting::get('address', ''),
            'opening_hours' => SiteSetting::get('opening_hours', ''),
        ];

        return response()->json([
            'success' => true,
            'data' => $siteInfo
        ]);
    }

    /**
     * Lấy tất cả settings (cho admin)
     */
    public function getAllSettings(): JsonResponse
    {
        $settings = SiteSetting::getAll();

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Sử dụng helper function
     */
    public function getSiteNameViaHelper(): JsonResponse
    {
        // Sử dụng helper function đã tạo
        $siteName = site_name();

        return response()->json([
            'success' => true,
            'site_name' => $siteName,
            'message' => 'Retrieved using helper function'
        ]);
    }

    /**
     * Demo sử dụng trong realtime notifications
     */
    public function getNotificationData(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'notification_config' => [
                'site_name' => site_name(),
                'welcome_message' => 'Chào mừng đến với ' . site_name(),
                'notification_title' => 'Thông báo từ ' . site_name(),
            ]
        ]);
    }
}
