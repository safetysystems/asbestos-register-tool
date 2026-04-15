<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyAsbestosAudit extends Model
{
    protected $fillable = [
        'property_id',
        'audit_date',
        'audit_hours',
        'job_type',
        'labelling_status',
        'qr_number',
        'installation_status',
        'lead_status',
        'samples_taken',
        'smf_status',
        'smf_notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'audit_date' => 'date',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(CustomerProperty::class, 'property_id');
    }

    public function samples(): HasMany
    {
        return $this->hasMany(AsbestosAuditSample::class, 'audit_id');
    }
}
