<?php

namespace Sitedigitalweb\Estadistica;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class EstadisticaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('estadistica', function ($app) {
            return new Estadistica;
        });
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../views', 'estadistica');

        $this->publishes([
            __DIR__ . '/migrations/' => database_path('migrations/tenant'),
        ], 'estadistica-migrations');

        $this->app->booted(function () {
            $this->registerRoutes();
        });
    }

    protected function registerRoutes(): void
    {
        Route::middleware([
            InitializeTenancyByDomain::class,
            PreventAccessFromCentralDomains::class,
            'web',
            'auth',
        ])->group(function () {
            $this->tenantRoutes();
        });
    }

    protected function tenantRoutes(): void
    {
        // Rutas de estadísticas
        Route::prefix('sd')->group(function () {
            Route::get('/stadistics',               [\Sitedigitalweb\Estadistica\Http\EstadisticaController::class, 'index']);
            Route::get('/stadistics-block',         [\Sitedigitalweb\Estadistica\Http\EstadisticaController::class, 'blocks']);
            Route::get('/stadistics-block/create',  fn() => view('estadistica::crear-block'));
            Route::post('/stadistics-block/creates',[\Sitedigitalweb\Estadistica\Http\EstadisticaController::class, 'crearblocks']);
        });
    }
}