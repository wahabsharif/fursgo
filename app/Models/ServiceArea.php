<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceArea extends Model
{
    /**
     * One international statute mile in metres (used for map radius and any server-side distance math).
     *
     * @see https://www.nist.gov/pml/special-publication-811/nist-guide-si-appendix-b9-factors-units-listed-kind-quantity-or
     */
    public const METERS_PER_STATUTE_MILE = 1609.344;

    protected $fillable = [
        'groomer_spacer_id',
        'name',
        'radius',
        'latitude',
        'longitude',
        'address',
        'map_color',
    ];

    protected $casts = [
        'radius' => 'float',
        'latitude' => 'float',
        'longitude' => 'float',
    ];
}
