<?php

namespace DigitalsiteSaaS\Estadistica\Tenant;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class Page extends Model

{
	use UsesTenantConnection;

	protected $table = 'pages';
    public $timestamps = true;

}