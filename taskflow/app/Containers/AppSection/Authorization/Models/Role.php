<?php

namespace App\Containers\AppSection\Authorization\Models;

use Apiato\Core\Models\InteractsWithApiato;
use Apiato\Http\Resources\ResourceKeyAware;
use App\Containers\AppSection\Authorization\Data\Collections\RoleCollection;
use Spatie\Permission\Models\Role as SpatieRole;

final class Role extends SpatieRole implements ResourceKeyAware
{
    use InteractsWithApiato;

    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'description',
    ];

    const ROLE_OWNER = 'owner';
    const ROLE_ADMIN = 'admin';
    const ROLE_MEMBER = 'member';

    const ROLE_PROJECT_MANAGER = 'project-manager';

    const ROLE_DEVELOPER = 'developer';

    public function newCollection(array $models = []): RoleCollection
    {
        return new RoleCollection($models);
    }
}
