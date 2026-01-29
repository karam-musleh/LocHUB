<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LocationRequest extends FormRequest
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

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'name'      => 'sometimes|array',
                'name.ar'   => 'sometimes|string|max:255',
                'name.en'   => 'sometimes|string|max:255',
                'parent_id' => 'nullable|exists:locations,id',
                'type'      => 'sometimes|string|in:governorate,city,area',
            ];
        } else {
            return [
                'name'      => 'required|array',
                'name.ar'   => 'required|string|max:255',
                'name.en'   => 'required|string|max:255',
                'parent_id' => 'nullable|exists:locations,id',
                'type'      => 'required|string|in:governorate,city,area',
            ];
        }
    }
}
