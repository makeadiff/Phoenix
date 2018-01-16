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

    public static function getAll()
    {
        return City::select('id', 'name')->where('type', 'actual')->orderBy('name')->get();
    }

    public static function fetch($city_id)
    {
    	return City::where('id', $city_id)->first();
    }

}

