<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyAsbestosAuditRequest extends FormRequest
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
            'property_id' => ['required', 'integer', 'exists:customer_properties,id'],
            'audit_date' => ['nullable', 'date'],
            'audit_hours' => ['nullable', 'string', 'max:255'],
            'job_type' => ['nullable', 'string', 'max:255'],
            'labelling_status' => ['nullable', 'string', 'max:255'],
            'qr_number' => ['nullable', 'string', 'max:255'],
            'installation_status' => ['nullable', 'string', 'max:255'],
            'lead_status' => ['nullable', 'string', 'max:255'],
            'samples_taken' => ['nullable', 'string', 'max:255'],
            'smf_status' => ['nullable', 'string', 'max:255'],
            'smf_notes' => ['nullable', 'string'],
            'samples' => ['nullable', 'array'],
            'samples.*.id' => ['nullable', 'integer'],
            'samples.*.sample_number' => ['nullable', 'string', 'max:255'],
            'samples.*.building_area' => ['nullable', 'string', 'max:255'],
            'samples.*.location' => ['nullable', 'string', 'max:255'],
            'samples.*.surface' => ['nullable', 'string', 'max:255'],
            'samples.*.material' => ['nullable', 'string', 'max:255'],
            'samples.*.hazardous_material' => ['nullable', 'string', 'max:255'],
            'samples.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'samples.*.condition' => ['nullable', 'string', 'max:255'],
            'samples.*.access_level' => ['nullable', 'string', 'max:255'],
            'samples.*.friability' => ['nullable', 'string', 'max:255'],
            'samples.*.hazard_priority' => ['nullable', 'string', 'max:255'],
            'samples.*.comments' => ['nullable', 'string'],
        ];
    }
}
