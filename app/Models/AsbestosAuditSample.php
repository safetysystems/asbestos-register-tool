<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsbestosAuditSample extends Model
{
    protected $fillable = [
        'audit_id',
        'sample_number',
        'building_area',
        'location',
        'surface',
        'material',
        'hazardous_material',
        'quantity',
        'condition',
        'access_level',
        'friability',
        'hazard_priority',
        'comments',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'float',
        ];
    }

    public function audit(): BelongsTo
    {
        return $this->belongsTo(PropertyAsbestosAudit::class, 'audit_id');
    }
}
