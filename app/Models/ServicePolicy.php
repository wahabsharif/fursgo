<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePolicy extends Model
{
    protected $fillable = [
        'goormer_spacer_profiles_id',
        'cancellation_policy',
        'late_arrival_policy',
        'refund_policy',
        'service_limitations',
        'animal_welfare_statement',
        'hygiene_safety_standards',
        'compliance_declaration',
        'compliance_timeline',
    ];

    protected $casts = [
        'cancellation_policy' => 'array',
        'late_arrival_policy' => 'array',
        'refund_policy' => 'boolean',
        'service_limitations' => 'array',
        'animal_welfare_statement' => 'boolean',
        'hygiene_safety_standards' => 'array',
        'compliance_declaration' => 'boolean',
        'compliance_timeline' => 'array',
    ];
}
