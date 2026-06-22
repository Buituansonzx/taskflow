<?php

namespace App\Containers\ClientSection\Project\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class GetTaskActivitiesRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'action' => 'nullable|in:created,deleted,moved,status_changed,priority_changed,deadline_changed,assignee_added,assignee_removed,tag_added,tag_removed',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'actor_id' => 'nullable|integer|exists:users,id',
        ];
    }
}
