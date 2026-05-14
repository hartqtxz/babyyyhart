<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'workers_needed',
        'salary_min',
        'salary_max',
        'location',
        'job_type',
        'experience_level',
        'status',
    ];

    protected $appends = [
        'salary',
        'company',
        'posted_by_name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getSalaryAttribute(): ?string
    {
        if ($this->salary_min && $this->salary_max) {
            return '₱' . number_format($this->salary_min, 2) . ' - ₱' . number_format($this->salary_max, 2);
        }

        if ($this->salary_min) {
            return '₱' . number_format($this->salary_min, 2);
        }

        if ($this->salary_max) {
            return '₱' . number_format($this->salary_max, 2);
        }

        return null;
    }

    public function getCompanyAttribute(): ?string
    {
        return $this->user?->name ?? 'Employer';
    }

    public function getPostedByNameAttribute(): ?string
    {
        return $this->user?->name ?? 'Employer';
    }

    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class);
    }
}
