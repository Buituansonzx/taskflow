<?php

namespace App\Containers\ClientSection\Task\Models;

use App\Containers\AppSection\User\Models\User;
use App\Containers\ClientSection\Project\Models\Project;
use App\Ship\Parents\Models\Model as ParentModel;

final class TaskActivity extends ParentModel
{
    // Action constants
    const ACTION_CREATED           = 'created';
    const ACTION_DELETED           = 'deleted';
    const ACTION_MOVED             = 'moved';
    const ACTION_STATUS_CHANGED    = 'status_changed';
    const ACTION_PRIORITY_CHANGED  = 'priority_changed';
    const ACTION_DEADLINE_CHANGED  = 'deadline_changed';
    const ACTION_ASSIGNEE_ADDED    = 'assignee_added';
    const ACTION_ASSIGNEE_REMOVED  = 'assignee_removed';
    const ACTION_TAG_ADDED         = 'tag_added';
    const ACTION_TAG_REMOVED       = 'tag_removed';

    protected $guarded = [];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class);
    }
}
