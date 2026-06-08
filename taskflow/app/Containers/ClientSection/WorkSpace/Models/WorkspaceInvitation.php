<?php

namespace App\Containers\ClientSection\WorkSpace\Models;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Models\Model as ParentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class WorkspaceInvitation extends ParentModel
{
    protected $guarded = [];
    protected $casts = [
    'expired_at'  => 'datetime',
    'accepted_at' => 'datetime',
];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
