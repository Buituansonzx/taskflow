<?php

namespace App\Containers\ClientSection\WorkSpace\Mails;

use App\Containers\ClientSection\WorkSpace\Models\Workspace;
use App\Ship\Parents\Mails\Mail as ParentMail;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

final class RemoveMemberMail extends ParentMail
{
    public function __construct(
        public readonly Workspace $workspace
    )
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bạn đã được xóa khỏi '.$this->workspace->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.workspace.remove-member',
            with: [
                'workspaceName' => $this->workspace->name,
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
