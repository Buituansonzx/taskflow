<?php

namespace App\Containers\ClientSection\Project\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class CreateProjectRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên dự án là bắt buộc',
            'name.max' => 'Tên dự án không được vượt quá 255 ký tự',
        ];
    }
}
