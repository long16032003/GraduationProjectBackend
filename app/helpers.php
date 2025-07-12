<?php
if (! function_exists('get_limiter_key')) {
    /** Giới hạn số lần request */
    function get_limiter_key(\Illuminate\Http\Request $request): string
    {
        $scope = strtolower($request->method()) . ':' . ($request->user()?->id ?: $request->ip());

        if (is_null($route = $request->route())) {
            return 'no_route:' . $scope;
        }

        if (!is_null($name = $route->getName())) {
            return implode(':', [$name, $scope]);
        }

        return implode(':', [
            'uri',
            // Example: /product/{item:sku}/store/{location_id?} -> /product/_item_sku/store/_location_id_
            str_replace(['{', '}', '?', ':'], ['_', '', '_', '_'], $route->uri()),
            $scope
        ]);
    }
}

if (! function_exists('site_setting')) {
    /**
     * Lấy giá trị setting từ database
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function site_setting(string $key, $default = null)
    {
        return \App\Models\SiteSetting::get($key, $default);
    }
    // VD: site_setting('site_name')
}

if (! function_exists('site_name')) {
    /**
     * Lấy tên site từ database
     *
     * @return string
     */
    function site_name(): string
    {
        return site_setting('site_name', 'Restaurant Name');
    }
}
