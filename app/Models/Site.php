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
        'tenant_id',
        'subdomain',
        'updated_by',
        'template_id',
        'site_name',
        'tagline',
        'logo_path',
        'headshot',
        'hero_image',
        'primary_color',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'zip',
        'bio',
        'license_number',
        'brokerage',
        'years_experience',
        'specialties',
        // Stats fields
        'stat_properties_sold',
        'stat_sales_volume',
        'stat_happy_clients',
        'stat_average_rating',
        'stat_properties_sold_label',
        'stat_sales_volume_label',
        'stat_happy_clients_label',
        'stat_average_rating_label',
        // Social media
        'facebook',
        'instagram',
        'linkedin',
        'twitter',
        'youtube',
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
            'stat_properties_sold' => 'integer',
            'stat_sales_volume' => 'integer',
            'stat_happy_clients' => 'integer',
            'stat_average_rating' => 'decimal:1',
            'years_experience' => 'integer',
        ];
    }

    /**
     * Get the tenant that owns the site.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user that last updated the site (deprecated).
     * @deprecated Use updatedBy() instead
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user that last updated the site.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the template used by the site.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Get the public URL for this site.
     */
    public function url(): string
    {
        $domain = config('app.base_domain', config('app.url'));
        return "https://{$this->subdomain}.{$domain}";
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
