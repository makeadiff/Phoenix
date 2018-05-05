<?php
namespace App\Models;

use App\Models\Common;
use App\Models\User;

final class Event extends Common  
{
    protected $table = 'Event';
    public $timestamps = true;

    protected $fillable = ['name','description','starts_on','place','type', 'city_id', 'event_type_id', 'created_by_user_id', 'latitude', 'longitude', 'status'];

    public function creator()
    {
        $creator = $this->belongsTo('App\Models\User', 'created_by_user_id');
        return $creator->first();
    }

    public function search($data) {
        $search_fields = ['id', 'name', 'description', 'starts_on', 'place', 'city_id', 'event_type_id', 'created_by_user_id', 'status'];

        $q = app('db')->table('Event');
        $q->select('Event.id', 'Event.name', 'Event.description', 'Event.starts_on', 'Event.place', 'Event.city_id', 'Event.event_type_id', 'Event.created_by_user_id', 
        			'Event.status', app('db')->raw('Event_Type.name AS event_type'));
        if(!isset($data['status'])) $data['status'] = '1';

        foreach ($search_fields as $field) {
            if(empty($data[$field])) continue;

            else $q->where("Event." . $field, $data[$field]);
        }
        $q->where("Event.starts_on", '>', $this->year . '-05-01 00:00:00');

        $q->join('Event_Type', 'Event.event_type_id', '=', 'Event_Type.id');
        $q->orderBy('Event.starts_on', 'Event.name');
        $results = $q->get();

        return $results;
    }

    public function users() {
        $users = $this->belongsToMany('App\Models\User', 'UserEvent', 'event_id', 'user_id')->select('User.id','User.name', 'UserEvent.present', 'UserEvent.late', 'UserEvent.user_choice');
        $data = $users->get();

        $rsvp = [
            'no_data',
            'going',
            'maybe',
            'cant_go',
        ];

        for($i=0; $i<count($data); $i++) {
            $data[$i]->rsvp = $rsvp[$data[$i]->user_choice];
            $data[$i]->present = ($data[$i]->present == 3) ? 0 : 1;
         }

        return $data;
    }

}