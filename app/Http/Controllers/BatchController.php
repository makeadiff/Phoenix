<?php
namespace App\Http\Controllers;

use App\Models\Batch;
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

        return JSend::success("Edited the batch", array('batch' => $result));
    }
}
