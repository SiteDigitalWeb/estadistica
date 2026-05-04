<?php

namespace Sitedigitalweb\Estadistica;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Cms_ips extends Model
{
    use BelongsToTenant;

    protected $table = 'cms_ips';

    protected $fillable = [
        'tenant_id',
        'ip',
        'name',
    ];
}