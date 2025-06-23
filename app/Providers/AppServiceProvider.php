<?php
namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Services\WebService;
use App\Services\AuthService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Daftarkan helper function terlebih dahulu
        if (!function_exists('generatePermissionsFlags')) {
            function generatePermissionsFlags($permissions, $keys) {
                $flags = [];
                foreach ($keys as $key) {
                    $flags[$key] = in_array($key, $permissions);
                }
                return $flags;
            }
        }

        // View Composer dengan error handling
        View::composer('*', function ($view) {
            try {
                $token = session('token');

                // Default values
                $defaultData = [
                    'user' => [],
                    'web' => null,
                    'permissions' => []
                ];

                // Jika tidak ada token, return default data dengan permission flags false
                if (!$token) {
                    $keys = $this->getPermissionKeys();
                    $defaultFlags = array_fill_keys($keys, false);
                    $view->with(array_merge($defaultData, $defaultFlags));
                    return;
                }

                // Get user info dengan error handling
                $userCacheKey = 'user_info_' . md5($token);
                $user = Cache::remember($userCacheKey, 300, function () use ($token) {
                    try {
                        $authService = app(AuthService::class);
                        return $authService->getUserInfo($token);
                    } catch (\Exception $e) {
                        Log::error('Failed to get user info: ' . $e->getMessage());
                        return [];
                    }
                });

                // Get web info dengan error handling
                $webCacheKey = 'web_info_' . md5($token);
                $web = Cache::remember($webCacheKey, 300, function () use ($token) {
                    try {
                        $webService = app(WebService::class);
                        return $webService->getById($token, 1);
                    } catch (\Exception $e) {
                        Log::error('Failed to get web info: ' . $e->getMessage());
                        return null;
                    }
                });

                $permissions = $user['permissions'] ?? [];
                $keys = $this->getPermissionKeys();
                $flags = generatePermissionsFlags($permissions, $keys);

                $view->with(array_merge([
                    'user' => $user,
                    'web' => $web,
                    'permissions' => $permissions
                ], $flags));

            } catch (\Exception $e) {
                Log::error('View composer error: ' . $e->getMessage());
                // Fallback ke default data
                $keys = $this->getPermissionKeys();
                $defaultFlags = array_fill_keys($keys, false);
                $view->with(array_merge([
                    'user' => [],
                    'web' => null,
                    'permissions' => []
                ], $defaultFlags));
            }
        });

        // Custom Blade directive dengan error handling
        Blade::if('can', function ($permission) {
            try {
                $token = session('token');

                if (!$token) {
                    return false;
                }

                $userCacheKey = 'user_info_' . md5($token);

                // Gunakan cache yang sama dengan view composer
                $user = Cache::get($userCacheKey);

                // Jika cache tidak ada, get dari service
                if ($user === null) {
                    $user = Cache::remember($userCacheKey, 300, function () use ($token) {
                        try {
                            return app(AuthService::class)->getUserInfo($token);
                        } catch (\Exception $e) {
                            Log::error('Failed to get user info in blade directive: ' . $e->getMessage());
                            return [];
                        }
                    });
                }

                return in_array($permission, $user['permissions'] ?? []);

            } catch (\Exception $e) {
                Log::error('Blade directive error: ' . $e->getMessage());
                return false;
            }
        });
    }

    /**
     * Get semua permission keys
     */
    private function getPermissionKeys(): array
    {
        return [
            'manage_permissions',
            'create_user', 'update_user', 'view_user', 'delete_user',
            'create_role', 'update_role', 'view_role', 'delete_role',
            'create_barang', 'update_barang', 'view_barang', 'delete_barang',
            'create_gudang', 'update_gudang', 'view_gudang', 'delete_gudang',
            'create_satuan', 'update_satuan', 'view_satuan', 'delete_satuan',
            'create_jenis_barang', 'update_jenis_barang', 'view_jenis_barang', 'delete_jenis_barang',
            'create_transaction_type', 'update_transaction_type', 'view_transaction_type', 'delete_transaction_type',
            'create_transaction', 'view_transaction',
            'create_category_barang', 'update_category_barang', 'view_category_barang', 'delete_category_barang',
        ];
    }
}
