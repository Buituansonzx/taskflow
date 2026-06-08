<?php

namespace App\Containers\ClientSection\WorkSpace\Data\Repositories;

use App\Containers\ClientSection\WorkSpace\Models\WorkspaceInvitation;
use App\Ship\Parents\Repositories\Repository as ParentRepository;

/**
 * @template TModel of WorkspaceInvitation
 *
 * @extends ParentRepository<TModel>
 */
final class WorkspaceInvitationRepository extends ParentRepository
{
    protected $fieldSearchable = [
        // 'id' => '=',
    ];
}
