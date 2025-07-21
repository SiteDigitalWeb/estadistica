<?php


namespace DigitalsiteSaaS\Estadistica\Http;
use DigitalsiteSaaS\Pagina\Date;
use App\Http\Controllers\Controller;
use DigitalsiteSaaS\Estadistica\Stats;
use DigitalsiteSaaS\Pagina\Content;
use DigitalsiteSaaS\Estadistica\Cms_Ips;
use DigitalsiteSaaS\Estadistica\Page;
use DB;
use Input;
use Illuminate\Http\Request;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Repositories\HostnameRepository;
use Hyn\Tenancy\Repositories\WebsiteRepository;


class EstadisticaController extends Controller{


protected $tenantName = null;

 public function __construct(){
  $this->middleware('auth');

  $hostname = app(\Hyn\Tenancy\Environment::class)->hostname();
        if ($hostname){
            $fqdn = $hostname->fqdn;
            $this->tenantName = explode(".", $fqdn)[0];
        }

 }

public function index(Request $request)
{
    // Obtener fechas por defecto (últimos 3 días)
    $defaultMinDate = now()->subDays(3)->format('Y-m-d');
    $defaultMaxDate = now()->format('Y-m-d');
    
    // Obtener parámetros de filtro con valores por defecto
    $min_date = $request->input('min_price', $defaultMinDate);
    $max_date = $request->input('max_price', $defaultMaxDate);

    // Determinar el contexto (tenant o central)
    $website = app(\Hyn\Tenancy\Environment::class)->website();
    
    // Seleccionar los modelos adecuados
    $statsModel = $website ? \DigitalsiteSaaS\Estadistica\Tenant\Stats::class : Stats::class;
    $pageModel = $website ? \DigitalsiteSaaS\Estadistica\Tenant\Page::class : Page::class;

    // Consultas comunes con filtro de fechas
    $baseStatsQuery = $statsModel::whereBetween('fecha', [$min_date, $max_date]);
    
    // Obtener estadísticas
    $stats = [
        'visitas' => $baseStatsQuery->count(),
        'nuevousuario' => $baseStatsQuery->distinct('ip')->count('ip'),
        'conteopagina' => $pageModel::count(),
        'paginas' => $this->getGroupedStats($baseStatsQuery, 'pagina'),
        'referidos' => $this->getGroupedStats($baseStatsQuery, 'referido'),
        'ciudades' => $this->getGroupedStats($baseStatsQuery, 'ciudad'),
        'fuentes' => $this->getGroupedStats($baseStatsQuery->whereNotNull('utm_source'), 'utm_source'),
        'idiomas' => $this->getGroupedStats($baseStatsQuery, 'idioma'),
        'meses' => $this->getGroupedStats($baseStatsQuery, 'mes', 'cp', 'asc'),
        'paises' => $this->getGroupedStats($baseStatsQuery, 'pais'),
        'min_price' => $min_date, // Pasar las fechas a la vista
        'max_price' => $max_date
    ];

    // Solo para tenant
    if ($website) {
        $stats['ips'] = $baseStatsQuery
            ->select('ip', 'utm_source')
            ->selectRaw('count(ip) as sum')
            ->groupBy('ip', 'utm_source')
            ->orderBy('sum', 'desc')
            ->get();
    }

    return view('estadistica::estadisticaweb', $stats);
}

// Método auxiliar para agrupar estadísticas
protected function getGroupedStats($query, $field, $orderBy = 'sum', $orderDir = 'desc')
{
    return $query->clone()
        ->select($field)
        ->selectRaw('count(ip) as sum')
        ->groupBy($field)
        ->orderBy($orderBy, $orderDir)
        ->get();
}


public function blocks()
{
    $model = $this->tenantName 
        ? \DigitalsiteSaaS\Estadistica\Tenant\Cms_Ips::class 
        : Cms_Ips::class;
        
    return view('estadistica::block', [
        'ips' => $model::all()
    ]);
}

public function crearblocks()
{
    // Determinar el modelo según el contexto (tenant o no)
    $pagina = $this->tenantName 
        ? new \DigitalsiteSaaS\Estadistica\Tenant\Cms_Ips 
        : new Cms_Ips;

    // Guardar la IP recibida desde el formulario
    $pagina->ip = request('ips');
    $pagina->save();

    // Redirigir con mensaje de éxito
    return redirect('/sd/stadistics-block')->with('status', 'ok_create');
}

public function eliminar($id)
{
    // Determinar el modelo según el contexto (tenant o no)
    $pagina = $this->tenantName 
        ? \DigitalsiteSaaS\Estadistica\Tenant\CmsIps::findOrFail($id) 
        : Cms_Ips::findOrFail($id);

    // Eliminar el registro
    $pagina->delete();

    // Redirigir con mensaje de éxito
    return redirect('/sd/stadistics-block')->with('status', 'ok_delete');
}



	




}
