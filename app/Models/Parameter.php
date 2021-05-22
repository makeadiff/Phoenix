<?php
namespace App\Models;

use App\Models\Common;
use App\Models\Credit;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

final class Parameter extends Model
{
    use Common;
    
    protected $table = 'Parameter';
    public $timestamps = true;
    const CREATED_AT = 'added_on';
    const UPDATED_AT = 'updated_on';
    protected $fillable = ['name', 'description', 'positive', 'negative', 'vertical_id', 'status'];

    public function user_credits()
    {
        return $this->hasMany('App\Models\Credit');
    }

    public function vertical()
    {
        return $this->belongsTo('App\Models\vertical', 'vertical_id');
    }

    public static function search($data)
    {
        $q = app('db')->table('Credit_Parameter');

        $q->select('id', 'name', 'description', 'credit', 'vertical_id', 'status');
        
        if (!empty($data['id'])) {
            $q->where('id', $data['id']);
        }
        if (!empty($data['name'])) {
            $q->where('name', 'LIKE', '%' . $data['name'] . '%');
        }
        if (!empty($data['vertical_id']) and $data['vertical_id']) {
            $q->where('vertical_id', $data['vertical_id']);
        }

        $parameters = $q->get();
        
        return $parameters;
    }
}
