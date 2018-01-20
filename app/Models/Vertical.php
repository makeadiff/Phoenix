<?php
namespace App\Models;

use App\Models\Common;

final class Vertical extends Common  
{
    protected $table = 'Vertical';
    public $timestamps = false;

	public function groups()
    {
        return $this->hasMany('App\Models\Group');
    }

}

