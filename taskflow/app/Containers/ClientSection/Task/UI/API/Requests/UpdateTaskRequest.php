<?php

namespace App\Containers\ClientSection\Task\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class UpdateTaskRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [    
            'title'            => 'nullable|string|max:255',
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
            'title.string' => 'Tiêu đề task phải là chuỗi',
            'title.max' => 'Tiêu đề task tối đa 255 ký tự',
            'description.string' => 'Mô tả task phải là chuỗi',
            'priority.in' => 'Ưu tiên không hợp lệ',
            'status.in' => 'Trạng thái không hợp lệ',
            'deadline.date' => 'Ngày hết hạn phải là ngày',
            'deadline.after' => 'Ngày hết hạn phải sau ngày hôm nay',
            'estimated_hours.numeric' => 'Thời gian ước tính phải là số',
            'estimated_hours.min' => 'Thời gian ước tính phải lớn hơn hoặc bằng 0',
            'parent_task_id.integer' => 'ID task cha phải là số nguyên',
            'parent_task_id.exists' => 'ID task cha không tồn tại',
            'assignees.array' => 'Danh sách người thực hiện phải là mảng',
            'assignees.*.integer' => 'ID người thực hiện phải là số nguyên',
            'assignees.*.exists' => 'ID người thực hiện không tồn tại',
            'tags.array' => 'Danh sách tag phải là mảng',
            'tags.*.integer' => 'ID tag phải là số nguyên',
            'tags.*.exists' => 'ID tag không tồn tại',
        ];
    }
}
