<?php

namespace App\Containers\ClientSection\WorkSpace\Listeners;

use App\Containers\ClientSection\WorkSpace\Events\InviteToWorkspaceEvent;
use App\Containers\ClientSection\WorkSpace\Mails\WorkspaceInvitationMail;
use App\Ship\Parents\Listeners\Listener as ParentListener;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

final class InviteToWorkspaceEventListener extends ParentListener implements ShouldQueue
{
    public function __construct()
    {
    }

    public function __invoke(InviteToWorkspaceEvent $event): void
    {
        Mail::to($event->invitation->email)->send(
            new WorkspaceInvitationMail($event->invitation)
        );
    }
}
