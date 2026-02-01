<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Location;

class HubRequest extends FormRequest
{
    public function authorize(): bool
    {
        // أي يوزر مسجل ممكن يطلب هب
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|array',
            'name.ar' => 'required|string|max:255',
            'name.en' => 'required|string|max:255',
            'description' => 'nullable|array',
            'description.ar' => 'nullable|string',
            'description.en' => 'nullable|string',
            'location_id' => [
                'required',
                'exists:locations,id',
                function ($attribute, $value, $fail) {
                    $location = Location::find($value);
                    if (!$location || $location->type !== 'area') {
                        $fail('Location must be an area.');
                    }
                }
            ],
            'address_details' => 'required|array',
            'address_details.ar' => 'required|string|max:255',
            'address_details.en' => 'required|string|max:255',
        ];
    }

    // public function prepareForValidation()
    // {
    //     // ممكن تضيف هنا أي تحويلات أو clean للـ input
    // }
}
