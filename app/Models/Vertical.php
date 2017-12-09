<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Vertical extends Model  
{
    protected $table = 'Vertical';
    public $timestamps = false;

    public $year;

	public function __construct(array $attributes = array())
	{
	    parent::__construct($attributes);
	    $this->year = 2017; // :TODO:
	}
	
	public function groups()
    {
        return $this->hasMany('App\Models\Group');
    }

}

