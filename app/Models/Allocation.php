<?php

namespace App\Models;

use App\Models\Common;
use App\Models\Batch;

class Allocation extends Common
{
    protected $table = 'UserBatch';
    public $timestamps = false;
    const CREATED_AT = 'added_on';
    protected $fillable = ['user_id','batch_id','level_id','role'];

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
        $this->item = $this->where('batch_id', $batch_id)
                         ->where('user_id', $user_id)
                         ->where('level_id', $level_id)
                         ->where('role', $role)
                         ->get();

        return $this->item;
    }

    public function createAllocation($batch_id, $level_id, $user_id, $role)
    {
        $existing_allocation = $this->getAllocation($batch_id, $level_id, $user_id, $role);

        if (count($existing_allocation)) {
            return false;
        }

        $allocation_data = [
          'user_id'   => $user_id,
          'batch_id'  => $batch_id,
          'role'      => $role,
          'level_id'  => $level_id,
          'added_on'  => NOW()
        ];

        $allocation = Allocation::create($allocation_data);
        $allocation_id = $allocation->id;

        return $allocation_id;
    }
    public function assignMentor($batch_id, $user_id)
    {
        return $this->createAllocation($batch_id, 0, $user_id, 'mentor');
    }
    public function assignTeacher($batch_id, $level_id, $user_id)
    {
        return $this->createAllocation($batch_id, $level_id, $user_id, 'teacher');
    }

    public function deleteAssignment($batch_id, $level_id, $user_id, $role)
    {
        $existing_connection = $this->getAllocation($batch_id, $level_id, $user_id, $role);

        if (!count($existing_connection)) {
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
