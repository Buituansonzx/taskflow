<?php

namespace App\Containers\ClientSection\Task\Models;

use App\Containers\ClientSection\Project\Models\Project;
use App\Ship\Parents\Models\Model as ParentModel;

final class Tag extends ParentModel
{
    protected $guarded = [];

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function tasks(){
        return $this->belongsToMany(Task::class, 'task_tags');
    }
}
