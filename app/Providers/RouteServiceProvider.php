<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // Wajib: Untuk pengecekan peran (role)

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     * * Konstanta ini wajib ada untuk mencegah error 'Undefined constant HOME'.
     *
     * @var string
     */
    public const HOME = '/dashboard'; // <-- KODE WAJIB DIKEMBALIKAN

    /**
     * Method untuk menentukan jalur redirect setelah otentikasi.
     * Laravel akan memprioritaskan method ini daripada konstanta HOME.
     * * @return string
     */
    public function redirectPath()
    {
        // Pengecekan dilakukan setelah user ter-otentikasi
        if (Auth::check()) {
            // ASUMSI: Anda menggunakan kolom/method 'isAdmin' pada model User
            // Ganti Auth::user()->isAdmin jika pengecekan role Anda berbeda (misal: Auth::user()->role === 'admin')
            if (Auth::user()->isAdmin) { 
                // Redirect Admin ke halaman utama Admin
                return route('admin.absences.index'); 
            }
            
            // Redirect Karyawan (Non-Admin) ke halaman utama Karyawan
            return route('absence.index');
        }

        // Fallback jika terjadi kegagalan otentikasi
        return self::HOME; 
    }
    
    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}