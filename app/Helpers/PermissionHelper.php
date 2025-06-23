<?php

if (!function_exists('generatePermissionsFlags')) {
    function generatePermissionsFlags(array $permissions, array $keys): array
    {
        $flags = [];

        foreach ($keys as $key) {
            $flags['can_' . $key] = in_array($key, $permissions);
        }

        return $flags;
    }
}

if (!function_exists('hasPermission')) {
    /**
     * Check apakah user memiliki permission tertentu
     *
     * @param string $permission
     * @return bool
     */
    function hasPermission($permission)
    {
        try {
            $token = session('token');

            if (!$token) {
                return false;
            }

            $userCacheKey = 'user_info_' . md5($token);
            $user = cache()->get($userCacheKey, []);

            return in_array($permission, $user['permissions'] ?? []);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Permission check error: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('getCurrentUser')) {
    /**
     * Get current user data
     *
     * @return array
     */
    function getCurrentUser()
    {
        try {
            $token = session('token');

            if (!$token) {
                return [];
            }

            $userCacheKey = 'user_info_' . md5($token);
            return cache()->get($userCacheKey, []);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Get current user error: ' . $e->getMessage());
            return [];
        }
    }
}
