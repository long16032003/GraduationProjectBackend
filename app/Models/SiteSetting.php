<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    // Lấy giá trị setting theo key
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    // Cập nhật hoặc tạo mới setting
    public static function set($key, $value)
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        return $setting;
    }

    // Lấy tất cả settings
    public static function getAll()
    {
        return static::all()->pluck('value', 'key')->toArray();
    }
}
