<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Center extends Model  
{
    protected $table = 'Center';
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

    public function city()
    {
         $city = $this->belongsTo('App\Models\City', 'city_id');
         return $city->get();
    }

    public static function getAll()
    {
        return Center::where('status', '1')->orderBy('name')->get();
    }

    public static function getAllInCity($city_id)
    {
        return Center::where('city_id', $city_id)->where('status', '1')->orderBy('name')->get();
    }

    public static function fetch($center_id)
    {
    	return Center::where('id', $center_id)->first();
    }

}

