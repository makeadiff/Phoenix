<?php

namespace App\Models;

use App\Models\Common;
use App\Models\Batch;

class Allocation extends Common
{
    protected $table = 'UserBatch';
    public $timestamps = true;
    const CREATED_AT = 'added_on';
    const UPDATED_AT = null;
    protected $fillable = ['user_id','batch_id','level_id','subject_id', 'role'];

    public function batch()
    {
        return $this->beiongsToMany('App\Models\Batch');
    }
    public function level()
    {
        return $this->belongsToMany('App\Level');
    }

    public function teacher()
    {
        return $this->belongsToMany('App\Models\User')->where('UserBatch.role', 'teacher');
    }

    public function mentor()
    {
        return $this->belongsToMany('App\Models\User')->where('UserBatch.role', 'mentor');
    }

    public function getAllocation($batch_id, $level_id, $user_id, $role)
    {
        $allocation = $this->where('batch_id', $batch_id)
                         ->where('user_id', $user_id)
                         ->where('level_id', $level_id)
                         ->where('role', $role)
                         ->first();

        return $allocation;
    }

    public function createAllocation($batch_id, $level_id, $user_id, $role, $subject_id = 0)
    {
        $existing_allocation = $this->getAllocation($batch_id, $level_id, $user_id, $role);

        if (isset($existing_allocation->id)) {
            if ($existing_allocation->subject_id == $subject_id) { // Exact same row already exist in db.
                return false;
            } else { // Found teacher/batch/level link - but for differnt subject. Updating.
                $alloc = $this->find($existing_allocation->id);
                $alloc->subject_id = $subject_id;
                $alloc->save();
                return $existing_allocation->id;
            }
        }

        $allocation_data = [
          'user_id'   => $user_id,
          'batch_id'  => $batch_id,
          'role'      => $role,
          'level_id'  => $level_id,
          'subject_id'=> $subject_id,
          'added_on'  => date('Y-m-d H:i:s')
        ];

        $allocation = Allocation::create($allocation_data);
        return $allocation->id;
    }
    public function assignMentor($batch_id, $user_id)
    {
        return $this->createAllocation($batch_id, 0, $user_id, 'mentor');
    }
    public function assignTeacher($batch_id, $level_id, $user_id, $subject_id)
    {
        return $this->createAllocation($batch_id, $level_id, $user_id, 'teacher', $subject_id);
    }

    public function deleteAssignment($batch_id, $level_id, $user_id, $role)
    {
        $existing_connection = $this->getAllocation($batch_id, $level_id, $user_id, $role);

        if (!isset($existing_connection->id)) {
            return false;
        } else {
            $this->item = $this->where('batch_id', $batch_id)
                               ->where('level_id', $level_id)
                               ->where('user_id', $user_id)
                               ->where('role', $role)
                               ->delete();
        }
        return true;
    }
    public function deleteMentorAssignment($batch_id, $user_id)
    {
        return $this->deleteAssignment($batch_id, 0, $user_id, 'mentor');
    }
    public function deleteTeacherAssignment($batch_id, $level_id, $user_id)
    {
        return $this->deleteAssignment($batch_id, $level_id, $user_id, 'teacher');
    }
}
