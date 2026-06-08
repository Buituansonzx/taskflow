<?php

namespace App\Containers\ClientSection\Workspace\Listeners;

use App\Containers\ClientSection\Workspace\Events\RemoveMemberEvent;
use App\Containers\ClientSection\WorkSpace\Mails\RemoveMemberMail;
use App\Ship\Parents\Listeners\Listener as ParentListener;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

final class RemoveMemberEventListener extends ParentListener implements ShouldQueue
{
    public function __construct()
    {
    }

    public function __invoke(RemoveMemberEvent $event): void
    {
        Mail::to($event->email)->send(
            new RemoveMemberMail($event->workspace)
        );
    }
}
