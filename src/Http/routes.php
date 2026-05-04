 <?php
// __DIR__ . '/Http/routes.php'

use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

// ==================================================
// RUTA ÚNICA /colombia/prueba (central o tenant)
// ==================================================
Route::middleware(['web'])->get('/colombia/prueba', function () {
    $host = request()->getHost();
    
    // Buscar si el dominio actual está asociado a algún tenant
    $domainRecord = \Stancl\Tenancy\Database\Models\Domain::where('domain', $host)->first();
    
    if ($domainRecord && !tenancy()->initialized) {
        $tenant = \App\Models\Tenant::find($domainRecord->tenant_id);
        if ($tenant) {
            tenancy()->initialize($tenant);
        }
    }
    
    $users = \App\Models\User::all();
    dd($users);
    
    return tenancy()->initialized 
        ? 'Tenant actual: ' . tenant('id')
        : 'Estás en el dominio central: ' . $host;
});

// ==================================================
// RUTAS DE GESTIÓN DE USUARIO (SOLO PARA TENANTS)
// ==================================================
// Este grupo se aplicará a cualquier dominio que NO sea central
// y que tenga un tenant asociado en la tabla 'domains'.
Route::middleware([
    'web',
    InitializeTenancyByDomain::class,      // Inicializa el tenant según el dominio
    PreventAccessFromCentralDomains::class, // Bloquea si el dominio es central
    'auth',
    'administrador'
])->group(function () {
    
    // Rutas de gestión de usuario
    Route::resource('gestion/usuario', 'Sitedigitalweb\Usuario\Http\UsuarioController');
    Route::get('gestion/usuario/editar/{id}', 'Sitedigitalweb\Usuario\Http\UsuarioController@editar');
    Route::post('gestion/usuario/actualizar/{id}', 'Sitedigitalweb\Usuario\Http\UsuarioController@actualizar');
    Route::post('gestion/usuario/crear', 'Sitedigitalweb\Usuario\Http\UsuarioController@crear');
    Route::get('gestion/usuario/eliminar/{id}', 'Sitedigitalweb\Usuario\Http\UsuarioController@eliminar');
    Route::get('gestion/crear-usuario', 'Sitedigitalweb\Usuario\Http\UsuarioController@crearusuario');
    
    // ==================================================
    // RUTAS DE ESTADÍSTICAS CON PREFIJO 'sd'
    // ==================================================
    Route::prefix('sd')->group(function () {
        Route::get('/stadistics', 'Sitedigitalweb\Estadistica\Http\EstadisticaController@index');
        Route::get('stadistics-block', 'Sitedigitalweb\Estadistica\Http\EstadisticaController@blocks');
        Route::get('/stadistics-block/create', function(){
            return View::make('estadistica::crear-block');
        });
        Route::post('/stadistics-block/creates', 'Sitedigitalweb\Estadistica\Http\EstadisticaController@crearblocks');
    });
    
});

// ==================================================
// RUTA /logout (funciona en cualquier lugar)
// ==================================================
Route::middleware(['web'])->get('/logout', '\App\Http\Controllers\Auth\LoginController@logout');