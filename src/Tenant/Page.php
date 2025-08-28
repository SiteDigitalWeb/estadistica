<?php

namespace Sitedigitalweb\Estadistica\Tenant;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class Page extends Model

{
	use UsesTenantConnection;

	protected $table = 'cms_pages';
    public $timestamps = true;

}