<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            // العنوان باللغتين
            'title' => [$isUpdate ? 'sometimes' : 'required', 'array'],
            'title.ar' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'title.en' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],

            // الوصف اختياري
            'description' => ['nullable', 'array'],
            'description.ar' => ['nullable', 'string'],
            'description.en' => ['nullable', 'string'],

            // النوع والسعر والمدة
            'type' => [$isUpdate ? 'sometimes' : 'required', 'string', 'in:daily,weekly,monthly'],
            'price' => [$isUpdate ? 'sometimes' : 'required', 'integer', 'min:0'],
            'duration' => [$isUpdate ? 'sometimes' : 'required', 'integer', 'min:1'],
            'status' => [$isUpdate ? 'sometimes' : 'nullable', 'string', 'in:soon,active,expired'],
        ];
    }
}
