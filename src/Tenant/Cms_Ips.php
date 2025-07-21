<?php

namespace Sitedigitalweb\Estadistica\Tenant;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class Cms_Ips extends Model

{
	use UsesTenantConnection;

	protected $table = 'cms_ips';
	public $timestamps = false;


}


