<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('customers')->ignore($this->route('customer'))],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'suburb' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postcode' => ['nullable', 'string', 'max:255'],
            'properties' => ['nullable', 'array'],
            'properties.*.id' => ['nullable', 'integer'],
            'properties.*.name' => ['nullable', 'string', 'max:255'],
            'properties.*.address' => ['nullable', 'string', 'max:255'],
            'properties.*.suburb' => ['nullable', 'string', 'max:255'],
            'properties.*.state' => ['nullable', 'string', 'max:255'],
            'properties.*.postcode' => ['nullable', 'string', 'max:255'],
            'properties.*.property_type' => ['nullable', 'string', 'max:255'],
            'properties.*.contact_name' => ['nullable', 'string', 'max:255'],
            'properties.*.contact_phone' => ['nullable', 'string', 'max:255'],
            'properties.*.contact_email' => ['nullable', 'email', 'max:255'],
            'properties.*.latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'properties.*.longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }
}
