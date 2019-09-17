<?php
namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use JSend;

class StudentController extends Controller
{
    private $validation_messages = [
            'city_id.exists'    => "Can't find any city with that ID"
        ];

    public function add(Request $request)
    {
        $validation_rules = [
            'name'      => 'required|max:50',
            'center_id' => 'required|numeric|exists:Center,id'
        ];
        
        $validator = \Validator::make($request->all(), $validation_rules, $this->validation_messages);

        if ($validator->fails()) {
            return response(JSend::fail("Unable to create student - errors in input", $validator->errors()), 400);
        }

        $student = new Student;
        $result = $student->add($request->all());

        return JSend::success("Created the student successfully", array('student' => $result));
    }

    public function edit(Request $request, $student_id)
    {
        $student = new Student;
        $exists = $student->fetch($student_id);

        if (!$exists) {
            return response(JSend::fail("Can't find any student with the given ID"), 404);
        }

        $validation_rules = [
            'name'        => 'max:50',
            'center_id'   => 'numeric|exists:Center,id'
        ];

        $validator = \Validator::make($request->all(), $validation_rules, $this->validation_messages);

        if ($validator->fails()) {
            return response(JSend::fail("Unable to create student - errors in input.", $validator->errors()), 400);
        }
        
        $result = $student->find($student_id)->edit($request->all());

        return JSend::success("Edited the student", array('student' => $result));
    }
}
