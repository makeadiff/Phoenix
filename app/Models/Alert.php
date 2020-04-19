<?php
namespace App\Models;

use App\Models\Common;

/**
 * Compliance stuff (High priority)
 *			CPP, Code of Conduct, MADCred
 *		Data Not filled
 *			Classes, Event Feedback (?)
 *		Must Attend Events
 *			Classes, Events
 *		Low Priority Events
 *			Events
 *		Custom Alerts Pushed from Directors / Strats / Fellows
 *			Launchs, Surveys, 
 *			Backend needs to be built for this - so not going to be done 
 */
class Alert extends Common
{
    public function generate($user_id) {
        $alerts = [];
        $user_model = new User;
        $user = $user_model->find($user_id);

        // Compliance checks
        // Check for CPP signing... 
        $cpp_signed = app('db')->table("UserData")->where('name', 'child_protection_policy_signed')->where('user_id', $user_id)->get();

        if(!count($cpp_signed)) {
            $alerts[] = [
                'name'          => "CPP Not Signed",
                'description'   => "You have not agreed to the Child Protection Policy yet. Please sign the policy to continue in the organization",
                'url'           => "http://makeadiff.in/policy/child_protection_policy/",
                'type'          => "danger",
                'priority'      => 9
            ];
        }

        // Data not filled.

        // Mentor hasn't filled teacher attendance.
        $batches_mentored_by_user = $user->batches()->get()->pluck('id');
        $teacher_data_not_entered = app('db')->table('Class')->distinct('class_on')
            ->whereIn('batch_id', $batches_mentored_by_user)
            ->where('status', 'projected')
            ->where('class_on', '<=', date('Y-m-d H:i:s'))
            ->orderBy('class_on', 'DESC')->groupBy('class_on')
            ->limit(5) // Don't want a lot of results. So.
            ->get(); // Distinct don't works as expected, so using groupby to get the same result.
        foreach($teacher_data_not_entered as $cls) {
            $alerts[] = [
                'name'          => 'Teacher Data for ' . date('d M', strtotime($cls->class_on)) . ' not entered',
                'description'   => "You have not marked the Teacher Attendance for class on " . date('d M, h:i A', strtotime($cls->class_on)),
                'url'           => 'https://makeadiff.in/madapp/mobile/#/mentor?batch_id=' . $cls->batch_id . '&class_on=' . $cls->class_on,
                'type'          => 'warning',
                'priority'      => 8
            ];
        }

        // Teacher hasn't filled student attendance.
        $all_classes = $user->classes()->get(); // All classes this user has taken
        $student_data_not_entered = [];
        foreach($all_classes as $cls) {
            $student_class = app('db')->table('StudentClass')->where('class_id', $cls->id)->get();
            if(!count($student_class)) {
                $student_data_not_entered[] = $cls;
            }
            if(count($student_data_not_entered) >= 5) break; // Result limiting.
        }
        foreach($student_data_not_entered as $cls) {
            $alerts[] = [
                'name'          => 'Student Data for ' . date('d M', strtotime($cls->class_on)) . ' not entered',
                'description'   => "You have not marked the Student Attendance for class on " . date('d M, h:i A', strtotime($cls->class_on)),
                'url'           => 'https://makeadiff.in/madapp/mobile/#/teacher?class_id=' . $cls->id,
                'type'          => 'warning',
                'priority'      => 7
            ];
        }

        // Upcoming events that the user has been invited for.
        $events_user_has_been_invited_for = app('db')->table('Event')->join('UserEvent', 'UserEvent.event_id', 'Event.id')
            ->where('Event.starts_on', '=>', date('Y-m-d H:i:s'))->where('Event.status', '1')
            ->where('UserEvent.user_id', $user_id)
            ->orderBy('Event.starts_on', 'DESC')
            ->limit(5) // Don't want a lot of results. So.
            ->get();
        foreach($events_user_has_been_invited_for as $event) {
            $alerts[] = [
                'name'          => 'You are invited to ' . $event->name . ' on ' . date('d M', strtotime($event->strats_on)),
                'description'   => 'You have been invited to an event not ' . $event->name . ' : ' . $event->description,
                'url'           => 'https://makeadiff.in/apps/envite/rsvp.php?event_id=' . $event->id,
                'type'          => 'primary',
                'priority'      => 4
            ];
        }

        return $alerts;
    }

}
