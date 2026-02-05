<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [

            // name
            'name' => [$isUpdate ? 'sometimes' : 'required', 'array'],
            'name.ar' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'name.en' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],

            // description
            'description' => ['nullable', 'array'],
            'description.ar' => ['nullable', 'string'],
            'description.en' => ['nullable', 'string'],

            // لازم الخدمة تكون تابعة لهب
        ];
    }
}
