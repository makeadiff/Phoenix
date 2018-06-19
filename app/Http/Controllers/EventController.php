<?php
namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use JSend;

class EventController extends Controller
{   
    private $validation_messages = [
            'city_id.exists'            => "Can't find any city with that ID",
            'created_by_user_id.exists' => "Can't find any user with the given ID",
            'event_type_id.exists'      => "Can't find any Event Type with the given ID",
        ];

    public function add(Request $request)
    {
        $validation_rules = [
            'name'                  => 'required|max:50',
            // 'city_id'               => 'sometimes|required|numeric|exists:City,id', // Disabled because I can't get 0 to validate.
            'created_by_user_id'    => 'required|numeric|exists:User,id',
            'event_type_id'         => 'required|numeric|exists:Event_Type,id',
        ];
        
        $validator = \Validator::make($request->all(), $validation_rules, $this->validation_messages);

        if ($validator->fails()) {
            return response(JSend::fail("Unable to create event - errors in input", $validator->errors()), 400);
        }

        $event = new Event;
        $result = $event->add($request->all());

        return JSend::success("Created the event successfully", array('event' => $result));
    }

    public function edit(Request $request, $event_id)
    {
        $event = new Event;
        $exists = $event->fetch($event_id);

        if(!$exists) {
            return response(JSend::fail("Can't find any Event with the given ID"), 404);
        }

        $validation_rules = [
            'name'                  => 'max:50',
            'city_id'               => 'numeric|sometimes|exists:City,id',
            'created_by_user_id'    => 'numeric|exists:User,id',
            'event_type_id'         => 'numeric|exists:Event_Type,id',
        ];

        $validator = \Validator::make($request->all(), $validation_rules, $this->validation_messages);

        if ($validator->fails()) {
            return response(JSend::fail("Unable to create event - errors in input.", $validator->errors()), 400);
        }
        
        $result = $event->find($event_id)->edit($request->all());

        return JSend::success("Edited the event", array('event' => $result));
    }
}
