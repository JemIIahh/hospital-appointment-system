<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_date',
        'appointment_time',
        'status',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function medicalRecord(): HasOne
    {
        return $this->hasOne(MedicalRecord::class);
    }

    public function prescription(): HasOne
    {
        return $this->hasOne(Prescription::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('status', '!=', 'cancelled');
    }

    public function scopeUpcoming(Builder $query): void
    {
        $query->whereIn('status', ['pending', 'confirmed'])
              ->whereDate('appointment_date', '>=', now()->toDateString());
    }

    public function scopeHistorical(Builder $query): void
    {
        $query->where(function (Builder $q) {
            $q->whereDate('appointment_date', '<', now()->toDateString())
              ->orWhereIn('status', ['cancelled', 'completed', 'no_show']);
        });
    }
}
