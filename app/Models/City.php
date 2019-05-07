<?php
namespace App\Models;

use App\Models\Common;

final class City extends Common  
{
    protected $table = 'City';
    public $timestamps = false;

	public function users()
    {
        return $this->hasMany('App\Models\User')->where('User.status', '=', '1')->where('User.user_type', '=', 'volunteer');
    }
    public function centers()
    {
        return $this->hasMany('App\Models\Center')->where('Center.status', '=', '1');
    }

    public function fetch($id) {
        $this->id = $id;
        $this->item = $this->find($id);

        return $this->item;
    }

    public static function getAll()
    {
        return City::select('id', 'name')->where('type', 'actual')->orderBy('name')->get();
    }
}

