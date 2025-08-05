<?php

namespace App\Models;

use App\Enums\TravelRequestStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};

class TravelRequest extends Model
{
    use HasUuids, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'status' => TravelRequestStatus::class,
    ];

    protected $fillable = [
        'applicant_name',
        'destination',
        'start_date',
        'end_date',
        'status'
    ];
}
