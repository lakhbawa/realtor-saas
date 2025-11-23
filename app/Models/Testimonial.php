<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'property_id',
        'client_name',
        'client_photo',
        'client_location',
        'content',
        'transaction_type',
        'transaction_date',
        'rating',
        'video_url',
        'is_published',
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
            'rating' => 'integer',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
            'transaction_date' => 'date',
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
        static::creating(function ($testimonial) {
            if (!$testimonial->user_id && auth()->check()) {
                $testimonial->user_id = auth()->id();
            }
        });
    }

    /**
     * Get the user that owns the testimonial.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the property associated with this testimonial.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Scope a query to only include published testimonials.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope a query to only include featured testimonials.
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
     * Get star rating as HTML.
     */
    public function getStarsHtmlAttribute(): string
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            $stars .= $i <= $this->rating ? '★' : '☆';
        }
        return $stars;
    }

    /**
     * Get the transaction type label.
     */
    public function getTransactionTypeLabelAttribute(): ?string
    {
        return match($this->transaction_type) {
            'bought' => 'Bought a Home',
            'sold' => 'Sold a Home',
            'rented' => 'Rented a Home',
            'bought_sold' => 'Bought & Sold',
            default => null,
        };
    }

    /**
     * Get client initials for avatar fallback.
     */
    public function getClientInitialsAttribute(): string
    {
        $words = explode(' ', $this->client_name);
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return $initials;
    }
}
