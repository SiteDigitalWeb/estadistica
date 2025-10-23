<?php


namespace Sitedigitalweb\Estadistica\Http;
use Sitedigitalweb\Pagina\Date;
use App\Http\Controllers\Controller;
use Sitedigitalweb\Estadistica\Stats;
use Sitedigitalweb\Pagina\Content;
use Sitedigitalweb\Estadistica\Cms_Ips;
use Sitedigitalweb\Estadistica\Page;
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
    // 🔹 Fechas por defecto (últimos 3 días)
    $defaultMinDate = now()->subDays(3)->format('Y-m-d');
    $defaultMaxDate = now()->format('Y-m-d');
    
    // 🔹 Obtener parámetros de filtro con valores por defecto
    $min_date = $request->input('min_price', $defaultMinDate);
    $max_date = $request->input('max_price', $defaultMaxDate);

    // 🔹 Determinar el contexto (tenant o central)
    $website = app(\Hyn\Tenancy\Environment::class)->website();
    
    if ($website) {
        // Si está en tenant
        $statsModel = \Sitedigitalweb\Estadistica\Tenant\Stats::class;
        $pageModel  = \Sitedigitalweb\Estadistica\Tenant\Page::class;
    } else {
        // Si está en el contexto central (no tenant)
        $statsModel = \Sitedigitalweb\Estadistica\Stats::class;
        $pageModel  = \Sitedigitalweb\Estadistica\Page::class;
    }

    // 🔹 Consulta base con filtro de fechas
    $baseStatsQuery = $statsModel::whereBetween('fecha', [$min_date, $max_date]);
    
    // 🔹 Obtener estadísticas generales
    $stats = [
        'visitas'       => $baseStatsQuery->count(),
        'nuevousuario'  => $baseStatsQuery->distinct('ip')->count('ip'),
        'conteopagina'  => $pageModel::count(),
        'paginas'       => $this->getGroupedStats($baseStatsQuery, 'pagina'),
        'referidos'     => $this->getGroupedStats($baseStatsQuery, 'referido'),
        'ciudades'      => $this->getGroupedStats($baseStatsQuery, 'ciudad'),
        'fuentes'       => $this->getGroupedStats($baseStatsQuery->whereNotNull('utm_source'), 'utm_source'),
        'idiomas'       => $this->getGroupedStats($baseStatsQuery, 'idioma'),
        // ✅ Ajuste aquí — se quita cp del orden para evitar ONLY_FULL_GROUP_BY
        'meses'         => $this->getGroupedStats($baseStatsQuery, 'mes'),
        'paises'        => $this->getGroupedStats($baseStatsQuery, 'pais'),
        'min_price'     => $min_date,
        'max_price'     => $max_date,
    ];

    // 🔹 Solo para tenant: agrupar IPs
    if ($website) {
        $stats['ips'] = $baseStatsQuery
            ->select('ip', 'utm_source')
            ->selectRaw('COUNT(ip) as sum')
            ->groupBy('ip', 'utm_source')
            ->orderBy('sum', 'desc')
            ->get();
    }

    return view('estadistica::estadisticaweb', $stats);
}



// Método auxiliar para agrupar estadísticas
protected function getGroupedStats($query, $field, $orderBy = 'sum', $orderDir = 'desc')
{
    $queryClone = clone $query;

    return $queryClone
        ->select($field)
        ->selectRaw('COUNT(ip) as sum')
        ->groupBy($field)
        ->orderBy($orderBy, $orderDir)
        ->get();
}


public function blocks()
{
    $model = $this->tenantName 
        ? \Sitedigitalweb\Estadistica\Tenant\Cms_Ips::class 
        : Cms_Ips::class;
        
    return view('estadistica::block', [
        'ips' => $model::all()
    ]);
}

public function crearblocks()
{
    // Determinar el modelo según el contexto (tenant o no)
    $pagina = $this->tenantName 
        ? new \Sitedigitalweb\Estadistica\Tenant\Cms_Ips 
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
        ? \Sitedigitalweb\Estadistica\Tenant\CmsIps::findOrFail($id) 
        : Cms_Ips::findOrFail($id);

    // Eliminar el registro
    $pagina->delete();

    // Redirigir con mensaje de éxito
    return redirect('/sd/stadistics-block')->with('status', 'ok_delete');
}



	




}
