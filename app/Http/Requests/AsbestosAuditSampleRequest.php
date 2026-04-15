<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AsbestosAuditSampleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'audit_id' => ['required', 'integer', 'exists:property_asbestos_audits,id'],
            'sample_number' => ['nullable', 'string', 'max:255'],
            'building_area' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'surface' => ['nullable', 'string', 'max:255'],
            'material' => ['nullable', 'string', 'max:255'],
            'hazardous_material' => ['nullable', 'string', 'max:255'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'condition' => ['nullable', 'string', 'max:255'],
            'access_level' => ['nullable', 'string', 'max:255'],
            'friability' => ['nullable', 'string', 'max:255'],
            'hazard_priority' => ['nullable', 'string', 'max:255'],
            'comments' => ['nullable', 'string'],
        ];
    }
}
