<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Property extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'tenant_id',
        'created_by',
        'updated_by',
        'title',
        'slug',
        'description',
        'price',
        'status',
        'listing_status',
        'bedrooms',
        'bathrooms',
        'square_feet',
        'year_built',
        'features',
        'address',
        'city',
        'state',
        'zip',
        'featured_image',
        'video_url',
        'is_featured',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'bathrooms' => 'decimal:1',
            'bedrooms' => 'integer',
            'square_feet' => 'integer',
            'year_built' => 'integer',
            'features' => 'array',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check() && !auth()->user()->is_admin) {
                $currentTenant = auth()->user()->currentTenant();
                if ($currentTenant) {
                    $builder->where('tenant_id', $currentTenant->id);
                }
            }
        });

        static::creating(function ($property) {
            if (!$property->tenant_id && auth()->check()) {
                $currentTenant = auth()->user()->currentTenant();
                if ($currentTenant) {
                    $property->tenant_id = $currentTenant->id;
                }
            }

            if (!$property->created_by && auth()->check()) {
                $property->created_by = auth()->id();
            }

            if (empty($property->slug)) {
                $property->slug = Str::slug($property->title);
            }
        });

        static::updating(function ($property) {
            if (auth()->check()) {
                $property->updated_by = auth()->id();
            }
        });
    }

    /**
     * Get the tenant that owns the property.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user that created the property.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user that last updated the property.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user that owns the property (deprecated).
     * @deprecated Use tenant() instead
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all images for the property.
     */
    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    /**
     * Get contact submissions for this property.
     */
    public function contactSubmissions(): HasMany
    {
        return $this->hasMany(ContactSubmission::class);
    }

    /**
     * Scope a query to only include active properties.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include featured properties.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    /**
     * Get the featured image URL.
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        return $this->featured_image ? asset('storage/' . $this->featured_image) : null;
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 0);
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
     * Get the public URL for the property.
     */
    public function getPublicUrlAttribute(): string
    {
        return route('public.property', ['slug' => $this->slug]);
    }
}
