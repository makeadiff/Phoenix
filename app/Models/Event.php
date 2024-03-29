<?php
namespace App\Models;

use App\Models\Common;
use App\Models\User;
use App\Libraries\Email;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

final class Event extends Model
{
    use Common;
    
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
            'no_data'   => '0',
            'going'	    => '1',
            'maybe'	    => '2',
            'cant_go'   => '3',
        ];

    protected $fillable = ['name','description','starts_on','place','type', 'city_id',
                             'event_type_id', 'template_event_id', 'user_selection_options',
                             'created_by_user_id', 'latitude', 'longitude', 'status', 'frequency','repeat_until'];

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
        return $this->belongsToMany("App\Models\User", 'UserEvent')->where("UserEvent.present", '=', '1')
                    ->withPivot('present', 'late', 'user_choice', 'reason');
    }

    public function event_type()
    {
        return $this->belongsTo('App\Models\Event_Type', 'event_type_id');
    }

    public function computed_type_name()
    {
        $type = $this->belongsTo('App\Models\Event_Type', 'event_type_id')->first();
        if($type) {
            return $type->computed_name();
        } else {
            return "";
        }
    }

    public function eventsInCity($city_id)
    {
        return app('db')->table("Event")->where("status", '1')->where("starts_on", '>=', $this->yearStartTime())->where("city_id", $city_id)->get();
    }

    public function filter($data)
    {
        return $this->where("city_id", $data['city_id'])->get();
    }

    public function search($data)
    {
        $search_fields = ['id', 'name', 'description', 'starts_on', 'date', 'from_date', 'to_date', 'place', 'city_id', 
                            'event_type_id', 'template_event_id', 'created_by_user_id', 'status', 'invited_user_id'];

        $q = app('db')->table('Event');
        $q->select(
            'Event.id',
            'Event.name',
            'Event.description',
            'Event.starts_on',
            'Event.place',
            'Event.city_id',
            'Event.event_type_id',
            'Event.created_by_user_id',
            'Event.status',
            app('db')->raw('Event_Type.name AS event_type'),
            app('db')->raw('Event_Type.vertical_id AS vertical_id'),
            app('db')->raw('Event_Type.role AS role'),
            app('db')->raw('Event_Type.audience AS audience')
        );
        if (!isset($data['status'])) {
            $data['status'] = '1';
        }

        foreach ($search_fields as $field) {
            if (empty($data[$field])) {
                continue;
            }

            if ($field == 'name' or $field == 'description' or $field == 'place') {
                $q->where("Event." . $field, 'LIKE', "%" . $data[$field] . "%");
            } elseif ($field == 'invited_user_id') {
                $q->join("UserEvent", 'UserEvent.event_id', '=', 'Event.id');
                $q->where("UserEvent.user_id", $data[$field]);
            } elseif ($field == 'date') {
                $q->whereDate("Event.starts_on", date('Y-m-d', strtotime($data[$field])));
            } elseif ($field == 'from_date') {
                $q->whereDate("Event.starts_on", '>=', date('Y-m-d', strtotime($data[$field])));
            } elseif ($field == 'to_date') {
                $q->whereDate("Event.starts_on", '<=', date('Y-m-d', strtotime($data[$field])));
            } else {
                $q->where("Event." . $field, $data[$field]);
            }
        }
        $q->where("Event.starts_on", '>=', $this->yearStartTime());

        $q->join('Event_Type', 'Event.event_type_id', '=', 'Event_Type.id');
        $q->orderBy('Event.starts_on')->orderBy('Event.name');
        $results = $q->get();
        // dd($q->toSql(), $q->getBindings());

        return $results;
    }

    public function users($filter = [])
    {
        $users = $this->belongsToMany('App\Models\User', 'UserEvent', 'event_id', 'user_id');
        $users->select('User.id', 'User.name', 'UserEvent.present', 'UserEvent.late', 'UserEvent.user_choice', 
                        'UserEvent.rsvp_auth_key', 'UserEvent.reason', 'User.city_id', 'User.email', 'User.mad_email');
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
            $data[$i]->marked = ($data[$i]->present == 0) ? 0 : 1;
            $data[$i]->present = ($data[$i]->present == 3 or $data[$i]->present == 0) ? 0 : 1;
        }

        return $data;
    }

    public function add($data)
    {
        $created_by_user_id = Auth::id();
        if(!empty($data['created_by_user_id'])) $created_by_user_id = $data['created_by_user_id'];

        $event = Event::create([
            'name'          => $data['name'],
            'description'   => isset($data['description']) ? $data['description'] : '',
            'starts_on'     => isset($data['starts_on']) ? date('Y-m-d H:i:s', strtotime($data['starts_on'])) : date('Y-m-d H:i:s'),
            'place'         => isset($data['place']) ? $data['place'] : '',
            'city_id'       => $data['city_id'],
            'event_type_id' => $data['event_type_id'],
            'template_event_id' => isset($data['template_event_id']) ? $data['template_event_id'] : 0,
            'created_by_user_id'=> $created_by_user_id,
            'latitude'      => isset($data['latitude']) ? $data['latitude'] : '',
            'longitude'     => isset($data['longitude']) ? $data['longitude'] : '',
            'repeat_until'  => isset($data['repeat_until']) ? date('Y-m-d H:i:s', strtotime($data['repeat_until'])) : null,
            'frequency'     => isset($data['frequency']) ? $data['frequency']: 'none',
        ]);
                
        return $event;
    }

    public function invite($user_ids, $send_invite_email = true, $event_id = false)
    {
        $event_id = $this->chain($event_id);

        // Remove users already at the event.
        $users_already_in_event = Event::find($event_id)->users()->pluck('id')->all();
        $users_not_yet_invited = array_diff($user_ids, $users_already_in_event);

        $user_event_insert = [];
        $rsvp_auth_keys = [];

        foreach ($users_not_yet_invited as $user_id) {
            $rsvp_auth_key = substr(md5(uniqid()), 0, 10);

            $user_event_insert[] = [
                'user_id'   => $user_id,
                'event_id'  => $this->id ? $this->id: $event_id,
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
            $mail->from     = "noreply@makeadiff.in";
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
        $event_id = $this->chain($event_id);
        $this->revertCredits($event_id, $user_id);

        $q = app('db')->table("UserEvent");
        $q->where('event_id', '=', $event_id)->where('user_id', '=', $user_id);

        $fields = ['present', 'late', 'rsvp', 'reason'];

        $update = [];
        foreach ($fields as $key) {
            if (!isset($data[$key])) {
                continue;
            }

            if ($key == 'rsvp') {
                $key = 'user_choice'; // DB Field is called user_choice.
                if(isset($this->rsvp_number_codes[$data['rsvp']])) {
                    $data['user_choice'] = $this->rsvp_number_codes[$data['rsvp']];
                } else if(isset($data['rsvp'])) {
                    $data['user_choice'] = $data['rsvp'];
                }
            }

            $update[$key] = $data[$key];
        }

        // dump($q->toSql(), $q->getBindings(), $update, $data);

        // A better way to do this is using the ::save() - but for some season its not working. Hence, this.
        $user_connection = $q->update($update);

        // Credit assignment if any.
        $this->awardCredits($event_id, $user_id);

        return $user_connection;
    }

    public function markEventAttendance($user_attendance, $event_id = false)
    {
        $event_id = $this->chain($event_id);
        $count = 0;

        foreach($user_attendance as $user) {
            $data = [];
            if(isset($user['present'])) $data['present'] = (string) $user['present'];
            if(isset($user['late'])) $data['late'] = (string) $user['late'];
            if(isset($user['rsvp'])) $data['rsvp'] = (string) $user['rsvp'];
            if(isset($user['reason'])) $data['reason'] = $user['reason'];

            $this->updateUserConnection($user['user_id'], $data);
            $count++;
        }
        return $count;
    }

    // This will mark all the users given as an array in the first argument as attended - and everyone else in the event with no data as missed.
    public function updateAttendance($user_ids, $event_id = false)
    {
        $event_id = $this->chain($event_id);
        $event = new Event;
        foreach ($user_ids as $user_id) {
            $event->updateUserConnection($user_id, ['present' => '1'], $event_id);
        }
        $unmarked_users = $event->find($event_id)->users(['present' => '3']);
        foreach ($unmarked_users as $user_id) {
            $event->updateUserConnection($user_id, ['present' => '0'], $event_id);
        }
    }

    public function deleteUserConnection($user_id, $event_id = false)
    {
        $event_id = $this->chain($event_id);

        $q = app('db')->table("UserEvent");
        $q->where('event_id', '=', $this->id)->where('user_id', '=', $user_id);
        $q->delete();
    }

    public function awardCredits($event_id, $user_id, $revert = false)
    {
        $user_event = app('db')->table("UserEvent")
                        ->join("Event", "UserEvent.event_id", "=", "Event.id")
                        ->where('event_id', $event_id)->where('user_id', $user_id)->first();
        if (!$user_event) {
            return false;
        }

        $credit_options = [
            'item'      => 'Event',
            'item_id'   => $event_id,
            'revert'    => $revert // This will reset credits that used to exist.
        ];

        $param_for_missing_aftercare_circle_after_informing = 10;
        $param_for_missing_aftercare_circle_without_informing = 11;
        $aftercare_circle_event_type = 32;
        if ($user_event->event_type_id === $aftercare_circle_event_type) {
            $credit = new Credit;

            if ($user_event->present == 3) {
                if ($user_event->user_choice == 3) {
                    $credit->assign($user_event->user_id, $param_for_missing_aftercare_circle_after_informing, $credit_options);
                } else {
                    $credit->assign($user_event->user_id, $param_for_missing_aftercare_circle_without_informing, $credit_options);
                }
            }
        }
    }

    public function revertCredits($event_id, $user_id)
    {
        $user_event = app('db')->table("UserEvent")->where('event_id', $event_id)->where('user_id', $user_id)->first();

        // No pre-existing event data. No need to revert credits.
        if (!$user_event) {
            return false;
        } elseif ($user_event->present != 3) {
            return false;
        }

        $this->awardCredits($event_id, $user_id, true);
    }

    public function createRecurringInstances($event, $frequency = false, $repeat_until = false)
    {
        // Validations.
        if(!in_array($frequency, ['monthly', 'weekly', 'bi-weekly'])) {
            $this->error("Invilid frequency value. Must be 'monthly' OR 'weekly' OR 'bi-weekly'");
            return false;
        }
        if(!$event or !$event->id) {
            $this->error("Invalid event provided.");
            return false;   
        }

        if (!$repeat_until) {
            $repeat_until = ($this->year+1).'-04-30'; // Basically, year end.
        } else {
            $repeat_until = date('Y-m-d', strtotime($repeat_until));
            if($repeat_until < date('Y-m-d')) {
                return $this->error("Date is invalid or in the past.");
            }
        }
        $count = 1;

        $event_id = $event->id;
        unset($event->id);

        // First, we update the template event to assign it the recurring values.
        $event->repeat_until = $repeat_until;
        $event->frequency = $frequency;
        if(!preg_match('/ \#\d+$/', $event->name)) { // If the name is not already in the #<Number format>
            $event->name = $event->name . ' #' . $count;
        }
        
        $event_model = new Event;
        $thisEvent = $event_model->find($event_id);
        $thisEvent->edit($event);
        $event->template_event_id = $event_id;
        $event_instances = [];

        // Get the list of all invited users.
        $users = $thisEvent->users()->pluck('id')->all();

        while ($event->starts_on < $repeat_until) {
            if ($frequency == 'monthly') {
                $event->starts_on = date('Y-m-d H:i:s', strtotime("+1 month", strtotime($event->starts_on)));
            } elseif ($frequency == 'weekly') {
                $event->starts_on = date('Y-m-d H:i:s', strtotime("+1 week", strtotime($event->starts_on)));
            } elseif ($frequency == 'bi-weekly') {
                $event->starts_on = date('Y-m-d H:i:s', strtotime("+2 week", strtotime($event->starts_on)));
            }
            if($event->starts_on > $repeat_until) break; //:UGLY: If this is not there, one instance after the repeat_until will be created.
            $count++;
            $event_instances[] = $this->addEventInstance($event, $users, $count);
            if($count > 100) break; // Just in case.
        }
        return $event_instances;
    }

    private function addEventInstance($event, $invited_user_ids, $count)
    {
        $event->name = str_replace(' #'.($count-1), ' #'.$count, $event->name);
        $event_in_db = $this->search($event); // Just to make sure this event don't exist already.
        
        if (!count($event_in_db)) { // Yup, doesn't exist
            $event_in_db = $this->add($event); // So, add it.
        } else {
            $event_in_db = $event_in_db[0];
        }
        $this->invite($invited_user_ids, false, $event_in_db->id);

        return $event_in_db->id;
    }
}
