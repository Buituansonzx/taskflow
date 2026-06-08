<?php

namespace App\Containers\ClientSection\Workspace\Events;

use App\Containers\ClientSection\Workspace\Models\Workspace;
use App\Ship\Parents\Events\Event as ParentEvent;

final class RemoveMemberEvent extends ParentEvent
{
    public function __construct(
        public readonly Workspace $workspace,
        public readonly string $email
    ) {}
}
