<?php

namespace App\Containers\ClientSection\WorkSpace\Models;

use App\Containers\AppSection\User\Models\User;
use App\Containers\ClientSection\Project\Models\Project;
use App\Ship\Parents\Models\Model as ParentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Workspace extends ParentModel
{
    use SoftDeletes;
    protected $guarded = [];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(
            User::class,
            'model_has_roles',
            'team_id',
            'model_id'
        )->withPivot('role_id')
         ->where('model_type', User::class);
    }

    public function projects(){
        return $this->hasMany(Project::class);
    }
}
