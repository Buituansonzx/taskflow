<?php

namespace App\Containers\ClientSection\WorkSpace\Events;

use App\Containers\ClientSection\WorkSpace\Models\WorkspaceInvitation;
use App\Ship\Parents\Events\Event as ParentEvent;

final class InviteToWorkspaceEvent extends ParentEvent
{
    public function __construct(
        public readonly WorkspaceInvitation $invitation,
    ) {
    }
}
