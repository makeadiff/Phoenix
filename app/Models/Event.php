<?php
namespace App\Models;

use App\Models\Common;
use App\Models\User;

final class Event extends Common  
{
    protected $table = 'Event';
    public $timestamps = true;
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';

    private $rsvp = [
            'no_data',
            'going',
            'maybe',
            'cant_go',
        ];

    protected $fillable = ['name','description','starts_on','place','type', 'city_id', 'event_type_id', 'created_by_user_id', 'latitude', 'longitude', 'status'];

    public function creator()
    {
        $creator = $this->belongsTo('App\Models\User', 'created_by_user_id');
        return $creator->first();
    }

    public function search($data) 
    {
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

    public function users($filter = []) 
    {
        $users = $this->belongsToMany('App\Models\User', 'UserEvent', 'event_id', 'user_id');
        $users->select('User.id','User.name', 'UserEvent.present', 'UserEvent.late', 'UserEvent.user_choice');
        if(isset($filter['rsvp'])) {
            $key = array_search($filter['rsvp'], $this->rsvp);
            $users->where('UserEvent.user_choice', '=', $key);
        }
        if(isset($filter['present'])) $users->where('UserEvent.present', '=', $filter['present']);
        if(isset($filter['late'])) $users->where('UserEvent.late', '=', $filter['late']);
        if(isset($filter['user_id'])) $users->where('UserEvent.user_id', '=', $filter['user_id']);

        $data = $users->get();
        // dd($users->toSql(), $users->getBindings());

        for($i=0; $i<count($data); $i++) {
            $data[$i]->rsvp = $this->rsvp[$data[$i]->user_choice];
            $data[$i]->present = ($data[$i]->present == 3) ? 0 : 1;
         }

        return $data;
    }

    public function add($data) 
    {
        $event = Event::create([
            'name'          => $data['name'],
            'description'   => isset($data['description']) ? $data['description'] : '',
            'starts_on'     => isset($data['starts_on']) ? $data['starts_on'] : date('Y-m-d H:i:s'),
            'place'         => isset($data['place']) ? $data['place'] : '',
            'city_id'       => $data['city_id'],
            'event_type_id' => $data['event_type_id'],
            'created_by_user_id'   => $data['created_by_user_id'],
            'latitude'      => isset($data['latitude']) ? $data['latitude'] : '',
            'longitude'     => isset($data['longitude']) ? $data['longitude'] : '',
        ]);

        return $event;
    }

    public function invite($user_ids, $event_id = false)
    {
        $this->chain($event_id);

        $user_event_insert = [];
        foreach ($user_ids as $user_id) {
            $user_event_insert[] = [
                'user_id'   => $user_id,
                'event_id'  => $this->id,
                'present'   => '0',
                'created_from'  => '1'
            ];
        }

        app('db')->table('UserEvent')->insert($user_event_insert);
    }

    public function updateUserConnection($user_id, $data, $event_id = false)
    {
        $this->chain($event_id);

        $q = app('db')->table("UserEvent");
        $q->where('event_id', '=', $this->id)->where('user_id', '=', $user_id);

        $fields = ['present', 'late', 'rsvp'];

        $update = [];
        foreach ($fields as $key) {
            if(!isset($data[$key])) continue;

            if($key == 'rsvp') $data[$key] = array_search($data[$key], $this->rsvp);

            $update[$key] = $data[$key];
        }

        // A better way to do this is using the ::save() - but for some season its not working. Hence, this.

        return $q->update($update);
    }

    public function deleteUserConnection($user_id, $event_id = false)
    {
        $this->chain($event_id);

        $q = app('db')->table("UserEvent");
        $q->where('event_id', '=', $this->id)->where('user_id', '=', $user_id);
        $q->delete();
    }
}
