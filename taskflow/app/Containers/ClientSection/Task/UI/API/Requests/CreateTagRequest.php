<?php

namespace App\Containers\ClientSection\Task\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class CreateTagRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7|starts_with:#',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên tag bắt buộc',
            'name.string' => 'Tên tag phải là chuỗi',
            'name.max' => 'Tên tag tối đa 255 ký tự',
            'color.required' => 'Màu tag bắt buộc',
            'color.string' => 'Màu tag phải là chuỗi',
            'color.max' => 'Màu tag tối đa 7 ký tự',
            'color.starts_with' => 'Màu tag phải bắt đầu bằng #',
        ];
    }
}
