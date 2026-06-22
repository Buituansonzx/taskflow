<?php

namespace App\Containers\ClientSection\Task\Models;

use App\Containers\AppSection\User\Models\User;
use App\Containers\ClientSection\Project\Models\Project;
use App\Containers\ClientSection\Task\Models\Tag;
use App\Ship\Parents\Models\Model as ParentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Task extends ParentModel
{
    use SoftDeletes;
    protected $guarded = [];

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parentTask(){
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function subTasks(){
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function tags(){
        return $this->belongsToMany(Tag::class, 'task_tags');
    }

    public function assignees(){
        return $this->belongsToMany(User::class, 'task_assignees');
    }
}
