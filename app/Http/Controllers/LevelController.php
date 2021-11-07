<?php
namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\Student;
use Illuminate\Http\Request;
use JSend;

class LevelController extends Controller
{
    private $validation_messages = [
            'center_id.exists'    => "Can't find any shelter with that ID"
        ];
    private $validation_rules = [
            'grade'     => 'required|numeric|min:1|max:15',
            'name'      => 'required',
            'center_id' => 'required|numeric|exists:Center,id',
            'project_id'=> 'required|numeric|exists:Project,id',
            'medium'    => 'in:vernacular,english',
            'preferred_gender'    => 'in:male,female,any',
        ];

    public function add(Request $request)
    {
        $validator = \Validator::make($request->all(), $this->validation_rules, $this->validation_messages);

        if ($validator->fails()) {
            return JSend::fail("Unable to create class section - errors in input", $validator->errors(), 400);
        }

        $level = new Level;
        $result = $level->add($request->all());

        return JSend::success("Created the class section successfully", array('level' => $result));
    }

    public function edit(Request $request, $level_id)
    {
        $level = new Level;
        $exists = $level->fetch($level_id);

        if (!$exists) {
            return response(JSend::fail("Can't find any class section with the given ID"), 404);
        }

        $validation_rules = $this->validation_rules;
        unset($validation_rules['project_id']);
        unset($validation_rules['center_id']);
        unset($validation_rules['medium']);
        unset($validation_rules['preferred_gender']);

        $validator = \Validator::make($request->all(), $validation_rules, $this->validation_messages);

        if ($validator->fails()) {
            return JSend::fail("Unable to create class section - errors in input.", $validator->errors(), 400);
        }
        
        $result = $level->find($level_id)->edit($request->all());

        return JSend::success("Edited the class section", array('level' => $result));
    }


    public function assignStudents(Request $request, $level_id = null)
    {
        $level_model = new Level;
        $level = null;
        if (!$level_id) {
            $level_id = $request->input('level_id');
        }
        if ($level_id) {
            $level = $level_model->fetch($level_id);
        }

        if (!$level) {
            return JSend::fail("Can't find any class section with the given ID", [], 404);
        }

        $student_ids_raw = $request->input('student_ids');
        if (!is_array($student_ids_raw)) {
            $student_ids = explode(",", $student_ids_raw);
        } else {
            $student_ids = $student_ids_raw;
        }

        $existing_student_ids = $level->students()->get()->pluck('id')->toArray(); // students alreday in the level

        $add_student_ids = array_values(array_diff($student_ids, $existing_student_ids));
        $remove_student_ids = array_values(array_diff($existing_student_ids, $student_ids));

        // Validation - make sure all students exists
        $student_not_found = [];
        $student_model = new Student;
        foreach ($add_student_ids as $student_id) {
            $student = $student_model->fetch($student_id);
            if (!$student) {
                array_push($student_not_found, $student_id);
            }
        }
        // Validation :TODO:...
        // Are given students part of the same city as the level

        if (count($student_not_found)) {
            return JSend::fail("Can't find students with these IDs: " . implode(",", $student_not_found));
        }

        foreach ($add_student_ids as $student_id) {
            $level_model->assignStudent($level_id, $student_id);
        }
        foreach ($remove_student_ids as $student_id) {
            $level_model->unassignStudent($level_id, $student_id);
        }

        // Why did I do this instead of the standard remove everything and add again? Not really sure - but this seemed cleaner.

        return JSend::success("Updated student(s) in class section " . $level->name, array('level' => $level));
    }
}
