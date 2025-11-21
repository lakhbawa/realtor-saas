<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Site extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'template_id',
        'site_name',
        'tagline',
        'logo_path',
        'primary_color',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'zip',
        'bio',
        'facebook',
        'instagram',
        'linkedin',
        'twitter',
        'meta_title',
        'meta_description',
        'is_published',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the site.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the template used by the site.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Get the logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    /**
     * Get the full address as a string.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->zip,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Check if site setup is complete.
     */
    public function isSetupComplete(): bool
    {
        return !empty($this->site_name)
            && !empty($this->bio)
            && !empty($this->phone)
            && !empty($this->email);
    }

    /**
     * Get setup completion percentage.
     */
    public function getSetupProgressAttribute(): int
    {
        $steps = [
            'logo_path',
            'bio',
            'phone',
            'email',
            'address',
        ];

        $completed = 0;
        foreach ($steps as $step) {
            if (!empty($this->$step)) {
                $completed++;
            }
        }

        return round(($completed / count($steps)) * 100);
    }
}
