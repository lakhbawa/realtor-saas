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
        'user_id',
        'title',
        'slug',
        'description',
        'price',
        'status',
        'bedrooms',
        'bathrooms',
        'square_feet',
        'address',
        'city',
        'state',
        'zip',
        'featured_image',
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

        // Auto-scope to authenticated user (tenant context)
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check() && !auth()->user()->is_admin) {
                $builder->where('user_id', auth()->id());
            }
        });

        // Auto-set user_id on create
        static::creating(function ($property) {
            if (!$property->user_id && auth()->check()) {
                $property->user_id = auth()->id();
            }

            // Auto-generate slug if not provided
            if (empty($property->slug)) {
                $property->slug = Str::slug($property->title);
            }
        });
    }

    /**
     * Get the user that owns the property.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
