<?php

namespace Sitedigitalweb\Estadistica;

use Illuminate\Support\ServiceProvider;

/**
* 
*/
class EstadisticaServiceProvider extends ServiceProvider
{
	
	 public function register()
	{
		$this->app->bind('estadistica', function($app) {
			return new Estadistica;

		});
	}

	public function boot()
	{
		require __DIR__ . '/Http/routes.php';


		$this->loadViewsFrom(__DIR__ . '/../views', 'estadistica');

		$this->publishes([

			__DIR__ . '/migrations/2015_07_25_000000_create_usuario_table.php' => base_path('database/migrations/2015_07_25_000000_create_usuario_table.php'),

			]);


	}

}

