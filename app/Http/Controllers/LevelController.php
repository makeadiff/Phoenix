<?php
namespace App\Http\Controllers;

use App\Models\Level;
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
            return response(JSend::fail("Unable to create class section - errors in input", $validator->errors()), 400);
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
            return response(JSend::fail("Unable to create class section - errors in input.", $validator->errors()), 400);
        }
        
        $result = $level->find($level_id)->edit($request->all());

        return JSend::success("Edited the class section", array('level' => $result));
    }
}
