<?php
namespace App\Models;

use App\Models\Common;
use Illuminate\Database\Eloquent\Model;

final class Log extends Model
{
    use Common;
    
    protected $table = 'Log';
    public $timestamps = true;
    const CREATED_AT = 'added_on';
    const UPDATED_AT = null;
    protected $fillable = ['name', 'log', 'user_id','level'];

    public function search($data)
    {
        $q = app('db')->table('Log');

        $q->select('Log.id', 'Log.user_id', 'Log.added_on', 'Log.name', 'Log.log', 'User.city_id', 'User.name', 'User.phone', 'User.email', 'User.mad_email');
        $q->join("User", "User.id", '=', 'Log.user_id');
        
        if (!empty($data['user_id'])) {
            $q->where('Log.user_id', $data['user_id']);
        }
        if (!empty($data['name'])) {
            $q->where('User.name', $data['name']);
        }
        if (!empty($data['log'])) {
            $q->where('User.log', 'LIKE', '%' . $data['log'] . '%');
        }
        if (!empty($data['level'])) {
            $q->where('User.level', $data['level']);
        }

        $q->orderBy('Log.added_on', 'desc');

        // dd($q->toSql(), $q->getBindings());
        $log = $q->get();
        
        return $log;
    }

    public static function add($data)
    {
        $log_data = '';
        if (isset($data['log'])) { // Its already JSON
            $log_data = $data['log'];
        } elseif (isset($data['data'])) { // Its an array, encode it as JSON
            $log_data = json_encode($data['data']);
        }

        $log = Log::insert([
            'name'      => $data['name'],
            'log'       => $log_data,
            'user_id'   => isset($data['user_id']) ? $data['user_id'] : 0,
            'level'     => isset($data['level']) ? $data['level'] : 'info',
            'added_on'	=> date('Y-m-d H:i:s')
        ]);

        return $log;
    }
}
