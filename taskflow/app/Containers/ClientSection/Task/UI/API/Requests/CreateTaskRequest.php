<?php

namespace App\Containers\ClientSection\Task\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class CreateTaskRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [    
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'priority'         => 'nullable|in:low,medium,high,urgent',
            'status'           => 'nullable|in:backlog,todo,in_progress,in_review,done,cancelled',
            'deadline'         => 'nullable|date|after:today',
            'estimated_hours'  => 'nullable|numeric|min:0',
            'parent_task_id'   => 'nullable|integer|exists:tasks,id',
            'assignees'        => 'nullable|array',
            'assignees.*'      => 'integer|exists:users,id',
            'tags'             => 'nullable|array',
            'tags.*'           => 'integer|exists:tags,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề là bắt buộc',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự',
            'description.string' => 'Mô tả phải là chuỗi',
            'priority.in' => 'Ưu tiên không hợp lệ',
            'status.in' => 'Trạng thái không hợp lệ',
            'deadline.date' => 'Thời hạn phải là ngày',
            'deadline.after' => 'Thời hạn phải sau ngày hiện tại',
            'estimated_hours.numeric' => 'Thời gian ước tính phải là số',
            'estimated_hours.min' => 'Thời gian ước tính phải lớn hơn hoặc bằng 0',
            'parent_task_id.integer' => 'ID của công việc cha phải là số nguyên',
            'parent_task_id.exists' => 'ID của công việc cha không tồn tại',
            'assignees.array' => 'Người được giao phải là mảng',
            'assignees.*.integer' => 'ID của người được giao phải là số nguyên',
            'assignees.*.exists' => 'ID của người được giao không tồn tại',
            'tags.array' => 'Tags phải là mảng',
            'tags.*.integer' => 'ID của tag phải là số nguyên',
            'tags.*.exists' => 'ID của tag không tồn tại',
        ];  
    }
}
