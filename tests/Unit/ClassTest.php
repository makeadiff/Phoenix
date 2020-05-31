<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Models\Classes;
use App\Models\User;
use App\Models\Credit;

/**
 * @runInSeparateProcess
 */
class ClassTest extends TestCase
{
    use WithoutMiddleware;
    public $class_id = 455176;
    public $user_id = 136213;
    public $mentor_id = 136222;
    public $substitute_id = 136215;

    public function testTeacherAttendance()
    {
        // if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");
        $user_model = new User;
        $teacher_credit = $user_model->find($this->user_id)->credit;

        $substitute_credit = $user_model->find($this->substitute_id)->credit;

        $class_model = new Classes;
        $data = $class_model->saveTeacherAttendance($this->class_id, $this->user_id, [
            'status'        => 'attended',
            'zero_hour_attendance'  => '1',
            'substitute_id' => $this->substitute_id
        ], $this->mentor_id);

        // Insert into Class table happened.
        $class_data = $class_model->find($this->class_id);
        $this->assertEquals($class_data->status, 'happened');
        $this->assertEquals($class_data->updated_by_mentor, $this->mentor_id);

        // Data in UserClass Should be accurate
        $user_class_data = app('db')->table('UserClass')->where('class_id', $this->class_id)->where('user_id', $this->user_id)->first();
        $this->assertEquals($user_class_data->substitute_id, $this->substitute_id);
        $this->assertEquals($user_class_data->status, 'attended');
        $this->assertEquals($user_class_data->zero_hour_attendance, '1');

        // Test if both credit lost and credit gained rows were added to Credit table
        $para_credit_lost_for_getting_sub = 2;
        $para_credit_gained_for_subbing = 1;
        $credit_model = new Credit;
        $credit_data = $credit_model->search([
                'user_id' => $this->user_id, 
                'parameter_id' => $para_credit_lost_for_getting_sub, 
                'item' => 'Class', 
                'item_id' => $this->class_id
            ]);
        $this->assertEquals($credit_data[0]->change, -1);
        $this->assertEquals($credit_data[0]->added_by_user_id, $this->mentor_id);

        $credit_data_sub = $credit_model->search([
                'user_id' => $this->substitute_id, 
                'parameter_id' => $para_credit_gained_for_subbing, 
                'item' => 'Class', 
                'item_id' => $this->class_id
            ]);
        $this->assertEquals($credit_data_sub[0]->change, 1);

        // User Credit was changed.
        $this->assertEquals($teacher_credit - 1, $user_model->find($this->user_id)->credit); // For teacher
        $this->assertEquals($substitute_credit + 1, $user_model->find($this->substitute_id)->credit); // And Sub
    }

    public function testTeacherAttendanceModify()
    {
        $user_model = new User;
        $teacher_credit = $user_model->find($this->user_id)->credit;
        $substitute_credit = $user_model->find($this->substitute_id)->credit;

        $class_model = new Classes;
        $data = $class_model->saveTeacherAttendance($this->class_id, $this->user_id, [
            'status'        => 'attended',
            'zero_hour_attendance'  => '1',
            'substitute_id' => 0
        ], $this->mentor_id);

        // Data in UserClass Should be accurate
        $user_class_data = app('db')->table('UserClass')->where('class_id', $this->class_id)->where('user_id', $this->user_id)->first();
        $this->assertEquals($user_class_data->substitute_id, '0');

        // Test if old credit rows of both teacher and sub was deleted.
        $para_credit_lost_for_getting_sub = 2;
        $para_credit_gained_for_subbing = 1;
        $credit_model = new Credit;
        $credit_data = $credit_model->search([
                'user_id' => $this->user_id, 
                'parameter_id' => $para_credit_lost_for_getting_sub, 
                'item' => 'Class', 
                'item_id' => $this->class_id
            ]);
        $this->assertEquals(count($credit_data), 0);

        $credit_data_sub = $credit_model->search([
                'user_id' => $this->substitute_id, 
                'parameter_id' => $para_credit_gained_for_subbing,
                'item' => 'Class', 
                'item_id' => $this->class_id
            ]);
        $this->assertEquals(count($credit_data_sub), 0);

        // User Credit was changed.
        $this->assertEquals($teacher_credit + 1, $user_model->find($this->user_id)->credit); // For teacher
        $this->assertEquals($substitute_credit - 1, $user_model->find($this->substitute_id)->credit); // And Sub
    }


}
