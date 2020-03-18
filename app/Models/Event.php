<?php
namespace App\Models;

use App\Models\Common;
use App\Models\User;
use App\Libraries\Email;

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
    private $rsvp_number_codes = [
            'going'	=> 1,
            'maybe'	=> 2,
            'cant_go'=>3,
        ];

    protected $fillable = ['name','description','starts_on','place','type', 'city_id', 'event_type_id','vertical_id', 'template_event_id', 'user_selection_options', 'created_by_user_id', 'latitude', 'longitude', 'status'];

    public function city()
    {
        return $this->belongsTo('App\Models\City', 'city_id');
    }

    public function creator()
    {
        return $this->belongsTo('App\Models\User', 'created_by_user_id');
    }

    public function invitees()
    {
        return $this->belongsToMany("App\Models\User", 'UserEvent')->withPivot('present', 'late', 'user_choice', 'reason');
    }

    public function attendees()
    {
        return $this->belongsToMany("App\Models\User", 'UserEvent')->where("UserEvent.present", '=', '1')->withPivot('present', 'late', 'user_choice', 'reason');
    }

    public function eventsInCity($city_id)
    {
        return app('db')->table("Event")->where("status", '1')->where("starts_on", '>=', $this->year_start_time)->where("city_id", $city_id)->get();
    }

    public function eventType()
    {
        if (!$this->id) {
            return false;
        }

        return app('db')->table("Event_Type")->where("id", $this->event_type_id)->pluck('name')->first();
    }

    public function filter($data)
    {
        return $this->where("city_id", $data['city_id'])->get();
    }

    public function search($data)
    {
        $search_fields = ['id', 'name', 'description', 'starts_on', 'date', 'from_date', 'to_date', 'place', 'city_id', 'event_type_id', 'template_event_id', 
            'vertical_id', 'created_by_user_id', 'status', 'invited_user_id'];

        $q = app('db')->table('Event');
        $q->select('Event.id','Event.name','Event.description','Event.starts_on','Event.place','Event.city_id',
                    'Event.event_type_id','Event.created_by_user_id','Event.status',app('db')->raw('Event_Type.name AS event_type'));
        if (!isset($data['status'])) {
            $data['status'] = '1'; 
        }

        foreach ($search_fields as $field) {
            if (empty($data[$field])) {
                continue;
            }

            if ($field == 'name' or $field == 'description' or $field == 'place') {
                $q->where("Event." . $field, 'LIKE', "%" . $data[$field] . "%");
            
            } elseif($field == 'invited_user_id') {
                $q->join("UserEvent", 'UserEvent.event_id', '=', 'Event.id');
                $q->where("UserEvent.user_id", $data[$field]);
            
            } elseif($field == 'date') {
                $q->whereDate("Event.starts_on", date('Y-m-d', strtotime($data[$field])));
            } elseif($field == 'from_date') {
                $q->whereDate("Event.starts_on", '>=', date('Y-m-d', strtotime($data[$field])));
            } elseif($field == 'to_date') {
                $q->whereDate("Event.starts_on", '<=', date('Y-m-d', strtotime($data[$field])));
            
            } else {
                $q->where("Event." . $field, $data[$field]);
            }
        }
        $q->where("Event.starts_on", '>=', $this->year_start_time);

        $q->join('Event_Type', 'Event.event_type_id', '=', 'Event_Type.id');
        $q->orderBy('Event.starts_on')->orderBy('Event.name');
        $results = $q->get();
        // dd($q->toSql(), $q->getBindings());

        return $results;
    }

    public function users($filter = [])
    {
        $users = $this->belongsToMany('App\Models\User', 'UserEvent', 'event_id', 'user_id');
        $users->select('User.id', 'User.name', 'UserEvent.present', 'UserEvent.late', 'UserEvent.user_choice', 'UserEvent.rsvp_auth_key');
        if (isset($filter['rsvp'])) {
            $key = array_search($filter['rsvp'], $this->rsvp);
            $users->where('UserEvent.user_choice', '=', $key);
        }
        if (isset($filter['present'])) {
            if ($filter['present'] == '1') {
                $users->whereRaw("UserEvent.present = '1'");
            } else {
                $users->whereRaw("(UserEvent.present = '0' OR UserEvent.present = '3')");
            }
        }
        if (isset($filter['late'])) {
            $users->whereRaw("UserEvent.late = '$filter[late]'");
        }
        if (isset($filter['user_id'])) {
            $users->where('UserEvent.user_id', '=', $filter['user_id']);
        }

        $data = $users->get();
        // dd($users->toSql(), $users->getBindings());

        for ($i=0; $i<count($data); $i++) {
            $data[$i]->rsvp = $this->rsvp[$data[$i]->user_choice];
            $data[$i]->present = ($data[$i]->present == 3 or $data[$i]->present == 0) ? 0 : 1;
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
            'vertical_id'   => isset($data['vertical_id']) ? $data['vertical_id'] : 0,
            'template_event_id' => isset($data['template_event_id']) ? $data['template_event_id'] : 0,
            'created_by_user_id'=> $data['created_by_user_id'],
            'latitude'      => isset($data['latitude']) ? $data['latitude'] : '',
            'longitude'     => isset($data['longitude']) ? $data['longitude'] : '',
        ]);

        return $event;
    }

    public function invite($user_ids, $send_invite_email = true, $event_id = false)
    {
        $this->chain($event_id);

        $user_event_insert = [];
        $rsvp_auth_keys = [];

        foreach ($user_ids as $user_id) {
            $rsvp_auth_key = substr(md5(uniqid()), 0, 10);

            // :TODO: Check if the user_id, event_id combo is there already

            $user_event_insert[] = [
                'user_id'   => $user_id,
                'event_id'  => $this->id,
                'present'   => '0',
                'created_from'  => '1',
                'created_on'=> date('Y-m-d H:i:s'),
                'rsvp_auth_key' => $rsvp_auth_key
            ];

            if ($send_invite_email) {
                $this->sendInvite($this->id, $user_id, $rsvp_auth_key, 'queue');
            }
        }

        app('db')->table('UserEvent')->insert($user_event_insert);
    }

    /// Send emails to invited users.
    public function sendInvite($event_id, $user_id, $rsvp_auth_key, $send_or_queue='queue')
    {
        $user = new User;
        $info = $user->fetch($user_id);
        $event_info = $this->fetch($event_id);

        $both_emails = [$info->email, $info->mad_email];

        foreach ($both_emails as $email) {
            if (!trim($email)) {
                continue;
            }

            $mail = new Email;
            $mail->from     = "noreply <noreply@makeadiff.in>";
            $mail->to       = $email;
            $mail->subject  = "RSVP for " . $event_info->name;

            $mail_content = "<p>You have been invited to '<strong>" . $event_info->name . "</strong>'";
            if ($event_info->starts_on and $event_info->starts_on > date('Y-m-d H:i:s')) {
                $mail_content .= " on " . date('j M(D) h:i A', strtotime($event_info->starts_on));
            }
            if ($event_info->place) {
                $mail_content .= " at " . $event_info->place;
            }
            $mail_content .= "</p>";

            if (trim($event_info->description)) {
                $mail_content .= "<p>" . nl2br(trim($event_info->description)) . "</p>";
            }

            $base_url = "http://makeadiff.in/apps/envite/rsvp.php?event_id={$event_id}&action=Save&";
            $go_url = $base_url . "rsvp={$this->rsvp_number_codes['going']}";
            $maybe_url = $base_url . "rsvp={$this->rsvp_number_codes['maybe']}";
            $no_go_url = $base_url . "rsvp={$this->rsvp_number_codes['cant_go']}";

            $mail_content .= "<p>Please confirm your presence at the event...
<div style=\"text-align:center;padding:15px 0;\">
<a href='".$go_url."' style='display:inline-block;padding:7px 15px;background-color:#ED1849;color:#fff;border-radius:4px;-webkit-border-radius:4px;margin:0 5px;'>GOING</a>
<a href='".$maybe_url."' style='display:inline-block;padding:7px 15px;background-color:#ED1849;color:#fff;border-radius:4px;-webkit-border-radius:4px;margin:0 5px;'>MAYBE</a>
<a href='".$no_go_url."' style='display:inline-block;padding:7px 15px;background-color:#ED1849;color:#fff;border-radius:4px;-webkit-border-radius:4px;margin:0 5px;'>CAN'T GO</a>
</div></p>";

            $base_path = app()->basePath();
            $email_html = file_get_contents($base_path . '/resources/email_templates/template.html');
            $mail->html = str_replace(
                array('%BASE_FOLDER%', '%CONTENT%', '%DATE%', '%FIRST_NAME%', '%NAME%'),
                array($base_path, $mail_content, date('d/m/Y'), $info->name, $info->name),
                $email_html
            );

            $images = [
                $base_path . '/public/assets/header.jpg'
            ];
            $mail->images = $images;

            if ($send_or_queue == 'send') {
                $mail->send();
            } else {
                $mail->queue();
            }
        }
    }

    public function updateUserConnection($user_id, $data, $event_id = false)
    {
        $this->chain($event_id);

        $q = app('db')->table("UserEvent");
        $q->where('event_id', '=', $this->id)->where('user_id', '=', $user_id);

        $fields = ['present', 'late', 'rsvp'];

        $update = [];
        foreach ($fields as $key) {
            if (!isset($data[$key])) {
                continue;
            }

            if ($key == 'rsvp') {
                $data[$key] = array_search($data[$key], $this->rsvp);
            }

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
