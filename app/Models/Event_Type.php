<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event_Type extends Model
{
    protected $table = 'Event_Type';
    public $timestamps = false;

    protected $fillable = ['name','vertical_id','status'];

    public function verticals()
    {
        return $this->belongsTo('App\Models\Vertical', 'vertical_id');
    }

    public static function getAll()
    {
        $event_types =  Event_Type::select('id','name','vertical_id')->where('status', '1')->orderBy('name')->get();
        foreach($event_types as $key => $types){
            $vertical = $types->verticals()->select('name')->first();
            if($vertical){
                $event_types[$key]->vertical = $vertical->name;
            }
        }

        return $event_types;
    }

    public function events(){
        return $this->hasMany('App\Models\Event')->where('Event.status','=','1');
    }

}
