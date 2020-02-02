<?php
namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Level;
use App\Models\User;
use App\Models\Allocation;
use Illuminate\Http\Request;
use JSend;

class BatchController extends Controller
{
    private $validation_messages = [
            'center_id.exists'    => "Can't find any shelter with that ID"
        ];
    private $validation_rules = [
            'day'       => 'required|numeric|min:0|max:6',
            'class_time'=> 'required',
            'center_id' => 'required|numeric|exists:Center,id',
            'project_id'=> 'required|numeric|exists:Project,id',
        ];

    public function add(Request $request)
    {
        $validator = \Validator::make($request->all(), $this->validation_rules, $this->validation_messages);

        if ($validator->fails()) {
            return response(JSend::fail("Unable to create batch - errors in input", $validator->errors()), 400);
        }

        $batch = new Batch;
        $result = $batch->add($request->all());

        if($request->input('mentor_user_ids')){
          $this->assignMentors($request, $batch_id);

        }

        return JSend::success("Created the batch successfully", array('batch' => $result));
    }

    public function edit(Request $request, $batch_id)
    {
        $batch = new Batch;
        $exists = $batch->fetch($batch_id);

        if (!$exists) {
            return response(JSend::fail("Can't find any batch with the given ID"), 404);
        }

        $validation_rules = $this->validation_rules;
        unset($validation_rules['project_id']);
        unset($validation_rules['center_id']);

        $validator = \Validator::make($request->all(), $validation_rules, $this->validation_messages);

        if ($validator->fails()) {
            return response(JSend::fail("Unable to create batch - errors in input.", $validator->errors()), 400);
        }

        $result = $batch->find($batch_id)->edit($request->all());

        if($request->input('mentor_user_ids')){
            $this->assignMentors($request, $batch_id);
        }

        return JSend::success("Edited the batch", array('batch' => $result));
    }

    public function assignMentors(Request $request, $batch_id = false)
    {
        $batch_model = new Batch;
        $batch = false;
        if (!$batch_id) {
            $batch_id = $request->input('batch_id');
        }

        if ($batch_id) {
            $batch = $batch_model->fetch($batch_id);
        } else {
            return response(JSend::fail("Can't find any batch with the given ID"), 404);
        }

        $user_ids_raw = $request->input('mentor_user_ids');
        if (!is_array($user_ids_raw)) {
            $user_ids = explode(",", $user_ids_raw);
        } else {
            $user_ids = $user_ids_raw;
        }

        // Find the project of this batch ...
        $project_key_mapping = config('constants.project_id_to_key');
        $project_key = $project_key_mapping[$batch->project_id];

        // ... and find the mentor user group id of that project.
        $mentor_group_id = config("constants.group.$project_key.mentor.id");

        $user_not_found = [];
        $user_not_mentor= [];

        $user_model = new User;
        foreach ($user_ids as $uid) {
            $mentor = $user_model->fetch($uid);
            if (!$mentor) {
                array_push($user_not_found, $uid);
            } else {
                // Check if the user has the mentor user group.
                $mentor_group_found = false;
                $mentor_groups = $mentor->groups()->get();
                foreach ($mentor_groups as $grp) {
                    if ($grp->id == $mentor_group_id) {
                        $mentor_group_found = true;
                        break;
                    }
                }

                if (!$mentor_group_found) {
                    array_push($user_not_mentor, $uid);
                }
            }
        }

        if (count($user_not_found)) {
            return response(JSEND::fail("Can't find users with these IDs: " . implode(",", $user_not_found)));
        }

        $insert_count = 0;

        $allocation_model = new Allocation;
        foreach ($user_ids as $uid) {
            // If the given user are not mentor, give them the mentor user group
            if (in_array($uid, $user_not_mentor)) {
                $user_model->fetch($uid)->addGroup($mentor_group_id);
            }

            // if ($batch_model->assignMentor($batch_id, $uid)) {
            //     $insert_count++;
            // }
            if ($allocation_model->createAssignment($batch_id, $uid,"mentor")) {
                $insert_count++;
            }
        }
        $batch->mentor_user_ids = $user_ids;

        return JSend::success("Added $insert_count mentor(s) to the batch " . $batch->name, array('batch' => $batch));
    }

    public function assignTeachers(Request $request, $batch_id = false, $level_id = false)
    {
        $batch_model = new Batch;
        $batch = false;
        if (!$batch_id) {
            $batch_id = $request->input('batch_id');
        }
        if ($batch_id) {
            $batch = $batch_model->fetch($batch_id);
        }

        if (!$batch) {
            return response(JSend::fail("Can't find any batch with the given ID"), 404);
        }


        $level_model = new Level;
        $level = false;
        if (!$level_id) {
            $level_id = $request->input('level_id');
        }
        if ($level_id) {
            $level = $level_model->fetch($level_id);
        } else {
            return response(JSend::fail("Can't find any class section with the given ID"), 404);
        }

        $user_ids_raw = $request->input('user_ids');
        if (!is_array($user_ids_raw)) {
            $user_ids = explode(",", $user_ids_raw);
        } else {
            $user_ids = $user_ids_raw;
        }



        // The group ID of the teacher group of the project this batch belongs to.
        $project_key_mapping = config('constants.project_id_to_key');
        $project_key = $project_key_mapping[$batch->project_id];

        $teacher_group_id = config("constants.group.$project_key.teacher.id");

        // Validation - make sure all teachers exists - and are teachers in the project of the batch.
        $user_not_found = [];
        $user_not_teacher=[];
        $user_model = new User;
        foreach ($user_ids as $uid) {
            $teacher = $user_model->fetch($uid);
            if (!$teacher) {
                array_push($user_not_found, $uid);
            } else {
                // Check if the user has the teacher user group.
                $teacher_group_found = false;
                $teachers_groups = $teacher->groups()->get();
                foreach ($teachers_groups as $grp) {
                    if ($grp->id == $teacher_group_id) {
                        $teacher_group_found = true;
                        break;
                    }
                }

                if (!$teacher_group_found) {
                    array_push($user_not_teacher, $uid);
                }
            }
        }
        // Validation :TODO:...
        // Are given users part of the same city as the batch
        // Are the given batch and level in the same shelter.
        // Is the level and batch associated with each other? If not, auto assign. PS: Decide if the LevelBatch linking is needed at all as well.

        if (count($user_not_found)) {
            return response(JSEND::fail("Can't find users with these IDs: " . implode(",", $user_not_found)));
        }

        $insert_count = 0;
        $allocation_model = new Allocation;
        foreach ($user_ids as $uid) {
            // If the given user are not teacher, give them the teacher user group
            if (in_array($uid, $user_not_teacher)) {
                $user_model->fetch($uid)->addGroup($teacher_group_id);
            }

            if ($allocation_model->createAssignment($batch_id, $uid, "teacher", $level_id)) {
                $insert_count++;
            }
        }

        return JSend::success("Added $insert_count teacher(s) to the batch " . $batch->name . " in class section " . $level->name, array('batch' => $batch));
    }
}
