<?php

namespace App\Models;

use App\Models\Common
use Illuminate\Database\Eloquent\Model;

class Event_Type extends Model
{
    use Common;
    
    protected $table = 'Event_Type';
    public $timestamps = false;

    protected $fillable = ['name','vertical_id', 'role', 'audience', 'status'];

    public function vertical()
    {
        return $this->belongsTo('App\Models\Vertical', 'vertical_id');
    }

    public function computed_name()
    {
        $vertical = $this->vertical()->first();

        $roles = [
            'volunteer' => 'Volunteer',
            'strat'     => 'Strat',
            'fellow'    => 'Fellow',
            'national'  => 'Director'
        ];
        $audiences = [
            'city'      => 'City',
            'center'    => 'Shelter',
            'vertical'  => 'Vertical'
        ];

        $name = '';
        if($vertical) $name = $vertical->name . ' ';
        if($this->audience) $name .= $audiences[$this->audience] . ' ';
        if($this->role) $name .= $roles[$this->role] . ' ';

        $name .= $this->name;
        return $name;
    }

    public static function getAll()
    {
        $event_types =  Event_Type::select('id', 'name', 'vertical_id', 'role', 'audience')->where('status', '1')->orderBy('name')->get();
        foreach ($event_types as $key => $types) {
            $vertical = $types->vertical()->select('name')->first();
            if ($vertical) {
                $event_types[$key]->vertical = $vertical->name;
            }
        }

        return $event_types;
    }

    public function events()
    {
        return $this->hasMany('App\Models\Event')->where('Event.status', '=', '1');
    }
}
