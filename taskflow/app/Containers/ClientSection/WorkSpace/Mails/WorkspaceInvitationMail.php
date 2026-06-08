<?php

namespace App\Containers\ClientSection\WorkSpace\Mails;

use App\Containers\ClientSection\WorkSpace\Models\WorkspaceInvitation;
use App\Ship\Parents\Mails\Mail as ParentMail;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

final class WorkspaceInvitationMail extends ParentMail
{
    public function __construct(
        public readonly WorkspaceInvitation $invitation,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bạn được mời vào workspace ' . $this->invitation->workspace->name,
        );

    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.workspace.invitation',
            with: [
                'workspaceName' => $this->invitation->workspace->name,
                'invitedBy'     => $this->invitation->invitedBy->name,
                'role'          => $this->invitation->role,
                'acceptUrl'     => config('app.url') . '/v1/workspaces/invitations/' . $this->invitation->token . '/accept',
                'expiredAt'     => $this->invitation->expired_at->format('d/m/Y H:i'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
