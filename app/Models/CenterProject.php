<?php
namespace App\Models;

use App\Models\Center;
use App\Models\Project;
use App\Models\Common;
use Illuminate\Database\Eloquent\Model;

final class CenterProject extends Model
{
    use Common;
    
    protected $table = 'CenterProject';
    public $timestamps = true;
    const CREATED_AT = 'added_on';
    const UPDATED_AT = null;

    public function project()
    {
        $projects = $this->belongsTo('App\Models\Project', 'project_id')->where('year', $this->year);
        return $projects;
    }

    public function name()
    {
        $name = app('db')->table('Project')->where("id", "=", $this->project_id)->first()->name;
        return $name;
    }

    public function pid() // Weird name because $this->id and $this->project_id already exist.
    {
        $id = app('db')->table('Project')->where("id", "=", $this->project_id)->first()->id;
        return $id;
    }

    // :TODO: For the love of all thats good and holy, write tests for this. Its messy and likely to break.
    public function batches()
    {
        $q = $this->hasMany('App\Models\Batch', 'center_id', 'center_id');
        $q->join("CenterProject", function ($join) { // Join using 2 columns - center_id and project_id
            $join->on('Batch.center_id', '=', 'CenterProject.center_id')
                ->where('CenterProject.project_id', '=', app('db')->raw('Batch.project_id'));
        });
        $q->where("Batch.year", $this->year)->where("Batch.status", 1);
        $q->where('Batch.project_id', '=', $this->project_id); // So that we can handle centers with multilpe centerproject
        $q->select(app('db')->raw("Batch.*"))->distinct(); // Because Otherwise CenterProject.id was owerwriting the ID

        // dump($q->toSql(), $q->getBindings());
        return $q;
    }

    public function levels()
    {
        $q = $this->hasMany('App\Models\Level', 'center_id', 'center_id');
        $q->join("CenterProject", function ($join) { // Join using 2 columns - center_id and project_id
            $join->on('Level.center_id', '=', 'CenterProject.center_id')
                ->where('Level.project_id', '=', app('db')->raw('CenterProject.project_id'));
        });
        $q->where("Level.year", $this->year)->where("Level.status", 1);
        $q->where('Level.project_id', '=', $this->project_id); // So that we can handle centers with multilpe centerproject
        $q->select(app('db')->raw("Level.*"))->distinct(); // Because Otherwise CenterProject.id was owerwriting the ID

        // dump($q->toSql(), $q->getBindings());
        return $q;
    }

    public function getAssign($center_id, $project_id, $year)
    {
        $assign = $this->where('center_id', $center_id)
                        ->where('project_id', $project_id)
                        ->where('year', $year)
                        ->first();

        return $assign;
    }   


    public function assignProject($center_id, $project_ids)
    {
        foreach($project_ids as $project_id)
        {
            $existing_assign = $this->getAssign($center_id, $project_id, $this->year);

            if (isset($existing_assign->id)) 
            {
                $result[] = $existing_assign->id;
            } 
            else
            {

                $data = [
                'center_id'   => $center_id,
                'project_id'  => $project_id,
                'added_on'  => date('Y-m-d H:i:s')
                ];
            
                $assign = CenterProject::create($data);
                $result[] = $assign->id;
            }
        }
        return $result;
    }
}