<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SiteSettingController extends Controller
{
    /**
     * Lấy tất cả settings
     */
    public function index(): JsonResponse
    {
        $settings = SiteSetting::getAll();

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Cập nhật settings
     */
    public function update(Request $request): JsonResponse
    {
        // Hỗ trợ cả 2 format: {settings: {...}} và {values: {settings: {...}}}
        $settingsData = $request->has('values.settings')
            ? $request->input('values.settings')
            : $request->input('settings');

        $request->validate([
            'settings' => 'required_without:values|array',
            'settings.*' => 'nullable|string',
            'values.settings' => 'required_without:settings|array',
            'values.settings.*' => 'nullable|string'
        ]);

        if (!$settingsData) {
            return response()->json([
                'success' => false,
                'message' => 'Không có dữ liệu settings để cập nhật'
            ], 422);
        }

        foreach ($settingsData as $key => $value) {
            SiteSetting::set($key, $value ?? '');
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thiết lập thành công'
        ]);
    }

    /**
     * Cập nhật một setting theo key
     */
    public function updateSingle(Request $request, $key): JsonResponse
    {
        $request->validate([
            'value' => 'nullable|string'
        ]);

        $value = $request->input('value', '');
        SiteSetting::set($key, $value);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thiết lập thành công',
            'data' => [
                'key' => $key,
                'value' => $value
            ]
        ]);
    }

    /**
     * Tạo mới một setting
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'key' => 'required|string|unique:site_settings,key',
            'value' => 'required|string'
        ]);

        $setting = SiteSetting::create([
            'key' => $request->key,
            'value' => $request->value
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tạo thiết lập mới thành công',
            'data' => $setting
        ], 201);
    }

    /**
     * Xóa một setting
     */
    public function destroy($key): JsonResponse
    {
        $setting = SiteSetting::where('key', $key)->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thiết lập này'
            ], 404);
        }

        $setting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa thiết lập thành công'
        ]);
    }

    /**
     * Lấy một setting theo key
     */
    public function show($key): JsonResponse
    {
        $value = SiteSetting::get($key);

        if ($value === null) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thiết lập này'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'key' => $key,
                'value' => $value
            ]
        ]);
    }
}
