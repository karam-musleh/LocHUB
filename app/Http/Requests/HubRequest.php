<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Location;

class HubRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'name' => [$isUpdate ? 'sometimes' : 'required', 'array'],
            'name.ar' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'name.en' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],

            'description' => ['nullable', 'array'],
            'description.ar' => ['nullable', 'string'],
            'description.en' => ['nullable', 'string'],

            'location_id' => [
                $isUpdate ? 'sometimes' : 'required',
                Rule::exists('locations', 'id')->where(fn($query) => $query->where('type', 'area')),
            ],

            'address_details' => [$isUpdate ? 'sometimes' : 'required', 'array'],
            'address_details.ar' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'address_details.en' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],

            'main_image' => [$isUpdate ? 'sometimes' : 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'gallery' => [$isUpdate ? 'sometimes' : 'nullable', 'array'],
            'gallery.*' => ['image', 'mimes:jpg,jpeg,png', 'max:2048'],

            'social_accounts' => ['nullable', 'array'],
            'social_accounts.*.platform' => ['required_with:social_accounts', 'string', 'max:255'],
            'social_accounts.*.url' => ['required_with:social_accounts', 'url', 'max:255'],
        ];
    }
}
