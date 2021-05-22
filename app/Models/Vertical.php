<?php
namespace App\Models;

use App\Models\Common;
use Illuminate\Database\Eloquent\Model;

final class Vertical extends Model
{
    use Common;
    
    protected $table = 'Vertical';
    public $timestamps = false;

    public function groups()
    {
        return $this->hasMany('App\Models\Group')->where('Group.status', '=', '1');
    }

    public static function getAll()
    {
        return Vertical::select('id', 'name')->where('status', '1')->orderBy('name')->get();
    }
}
