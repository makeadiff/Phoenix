<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Group extends Model  
{
    protected $table = 'Group';
    public $timestamps = false;

    public $year;

	public function __construct(array $attributes = array())
	{
	    parent::__construct($attributes);
	    $this->year = 2017; // :TODO:
	}
	
	public function users()
    {
        return $this->belongsToMany('App\Models\User')->wherePivot('year',$this->year);
    }

    public function vertical()
    {
         return $this->belongsTo('App\Models\Vertical', 'vertical_id');
    }

    // public function getAll()
    // {
    //     return Group::select('id', 'name')->get();
    // }
}

