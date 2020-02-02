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

    public function batch(){
      return $this->beiongsToMany('App\Models\Batch');
    }

    public function teacher(){
      return $this->belongsToMany('App\Models\User')->where('UserBatch.role','teacher');
    }

    public function mentor(){
      return $this->belongsToMany('App\Models\User')->where('UserBatch.role','mentor');
    }

    public function level(){
      return $this->belongsToMany('App\Level');
    }

    public function get($batch_id, $user_id,$role="teacher",$level_id=0){
      $this->item = $this->where('batch_id',$batch_id)
                         ->where('user_id',$user_id)
                         ->where('level_id',$level_id)
                         ->where('role',$role)
                         ->get();

      return $this->item;
    }

    public function createAssignment($batch_id, $user_id, $role="teacher", $level_id=0)
    {
        $mentor_batch_connection = $this->get($batch_id, $user_id, $role, $level_id);

        if (count($mentor_batch_connection)) {
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

    public function deleteAssignment($batch_id, $user_id, $role="teacher", $level_id=0)
    {
        // See if this mentor is in the batch already.
        $mentor_batch_connection = $this->get($batch_id, $user_id, $role, $level_id);

        if (!count($mentor_batch_connection)) {
            return false;
        }
        else{
            $this->item = $this->where('batch_id',$batch_id)
                               ->where('user_id',$user_id)
                               ->where('role',$role)
                               ->where('level_id',$level_id)
                               ->delete();
        }
        return true;
    }

}
