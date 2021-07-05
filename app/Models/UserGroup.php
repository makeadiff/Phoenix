<?php
namespace App\Models;

use App\Models\Common;
use Illuminate\Database\Eloquent\Model;

final class UserGroup extends Model
{
    use Common;
    protected $table = 'UserGroup';
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function group()
    {
        return $this->belongsTo('App\Models\Group');
    }
}
