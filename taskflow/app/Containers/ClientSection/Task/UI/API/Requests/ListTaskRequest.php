<?php

namespace App\Containers\ClientSection\Task\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class ListTaskRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'assignee_id'  => 'nullable|integer|exists:users,id',
            'status' => 'nullable|in:backlog,todo,in_progress,in_review,done,cancelled',
            'priority' => 'nullable|in:high,medium,low,urgent',
            'search' => 'nullable|string|max:255',
            'sort_order' => 'nullable|in:asc,desc',
            'sort_by'      => 'nullable|in:created_at,deadline,priority,status',
            'tag_ids'      => 'nullable|array',
            'tag_ids.*'    => 'integer|exists:tags,id',
            'deadline_from'=> 'nullable|date',
            'deadline_to'  => 'nullable|date|after_or_equal:deadline_from',
        ];
    }

    public function messages(): array
    {
        return [
            'assignee_id.integer' => 'Người được giao không hợp lệ',
            'assignee_id.exists' => 'Người được giao không tồn tại',
            'status.in' => 'Trạng thái không hợp lệ',
            'priority.in' => 'Độ ưu tiên không hợp lệ',
            'search.string' => 'Tìm kiếm không hợp lệ',
            'search.max' => 'Tìm kiếm không hợp lệ',
            'sort_order.in' => 'Thứ tự sắp xếp không hợp lệ',
            'sort_by.in' => 'Sắp xếp không hợp lệ',
            'tag_ids.array' => 'Tags phải là mảng',
            'tag_ids.*.integer' => 'ID của tag phải là số nguyên',
            'tag_ids.*.exists' => 'ID của tag không tồn tại',
            'deadline_from.date' => 'Deadline phải là ngày',
            'deadline_to.date' => 'Deadline phải là ngày',
            'deadline_to.after_or_equal' => 'Deadline phải sau ngày bắt đầu',
        ];
    }
}
