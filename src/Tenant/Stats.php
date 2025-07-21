<?php

namespace Sitedigitalweb\Estadistica\Tenant;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class Stats extends Model

{
	use UsesTenantConnection;

	protected $table = 'cms_statistics';
	public $timestamps = true;

	
}