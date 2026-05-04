<?php

namespace Sitedigitalweb\Pagina;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Stats extends Model
{
    use BelongsToTenant;

    protected $table = 'cms_statistics';

    protected $fillable = [
        'tenant_id',
        'ip', 'host', 'navegador', 'referido',
        'ciudad', 'pais', 'cp', 'latitud', 'longitud',
        'pagina', 'mes', 'ano', 'hora', 'dia',
        'idioma', 'fecha', 'utm_campana', 'utm_medium',
        'utm_source', 'remember_token',
    ];
}