<?php

namespace App\Containers\ClientSection\Project\Models;

use App\Containers\AppSection\User\Models\User;
use App\Containers\ClientSection\WorkSpace\Models\Workspace;
use App\Ship\Parents\Models\Model as ParentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Project extends ParentModel
{
    use SoftDeletes;
    protected $guarded = [];

    public function workspace(){
        return $this->belongsTo(Workspace::class);
    }

    public function members(){
        return $this->belongsToMany(User::class, 'project_members')->withPivot('role')->withTimestamps();
    }

    
}
