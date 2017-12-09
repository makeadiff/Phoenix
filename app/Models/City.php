<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class City extends Model  
{
    protected $table = 'City';
    public $timestamps = false;

    public $year;

	public function __construct(array $attributes = array())
	{
	    parent::__construct($attributes);
	    $this->year = 2017; // :TODO:
	}
	
	public function users()
    {
        return $this->hasMany('App\Models\User');
    }

}

