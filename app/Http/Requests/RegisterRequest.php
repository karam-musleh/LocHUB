<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // أي شخص مسموح بالتسجيل
    }

    public function rules(): array
    {
        $rules = [
            'role'       => 'required|in:user,hub_owner',
            'phone'      => 'nullable|string|max:20',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:8|confirmed',
            'location_id' => 'required|exists:locations,id',
            'specialization' => 'nullable|string|max:255',
        ];

        // حسب الدور
        if ($this->role === 'hub_owner') {
            $rules['name.ar'] = 'required|string|max:255';
            $rules['name.en'] = 'required|string|max:255';
        } else {
            $rules['name'] = 'required|string|max:255';
        }
        return $rules;
    }
}
