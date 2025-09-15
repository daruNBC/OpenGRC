<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'owner_name',
        'type',
        'description',
        'location',
        'dependencies',
        'status',
        'url',
        'notes',
        'custodian_name',
        'latitude', 
        'longitude',
        'user_code'
    ];

    protected $casts = [
        'type' => ApplicationType::class,
        // 'status' => ApplicationStatus::class,
        'logo' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
    protected static function booted()
    {
        static::creating(function ($application) {
            if (empty($application->code)) {
                // Generate a unique code, e.g., AST-B7C1D2
                $application->code = 'AST-' . strtoupper(Str::random(6));
            }
        });
    }
}
