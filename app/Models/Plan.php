<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'features',
        'limits',
        'stripe_monthly_price_id',
        'stripe_quarterly_price_id',
        'stripe_annual_price_id',
        'monthly_price',
        'quarterly_price',
        'annual_price',
        'trial_days',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'limits' => 'array',
        'monthly_price' => 'integer',
        'quarterly_price' => 'integer',
        'annual_price' => 'integer',
        'trial_days' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all subscriptions for this plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get formatted monthly price.
     */
    public function getFormattedMonthlyPriceAttribute(): string
    {
        return '$' . number_format($this->monthly_price / 100, 2);
    }

    /**
     * Get formatted quarterly price.
     */
    public function getFormattedQuarterlyPriceAttribute(): string
    {
        return '$' . number_format($this->quarterly_price / 100, 2);
    }

    /**
     * Get formatted annual price.
     */
    public function getFormattedAnnualPriceAttribute(): string
    {
        return '$' . number_format($this->annual_price / 100, 2);
    }

    /**
     * Get monthly equivalent for quarterly billing.
     */
    public function getQuarterlyMonthlyEquivalentAttribute(): string
    {
        $monthly = $this->quarterly_price / 3;
        return '$' . number_format($monthly / 100, 2);
    }

    /**
     * Get monthly equivalent for annual billing.
     */
    public function getAnnualMonthlyEquivalentAttribute(): string
    {
        $monthly = $this->annual_price / 12;
        return '$' . number_format($monthly / 100, 2);
    }

    /**
     * Get savings percentage for quarterly vs monthly.
     */
    public function getQuarterlySavingsPercentAttribute(): int
    {
        if ($this->monthly_price <= 0) {
            return 0;
        }
        $monthlyTotal = $this->monthly_price * 3;
        return (int) round((1 - ($this->quarterly_price / $monthlyTotal)) * 100);
    }

    /**
     * Get savings percentage for annual vs monthly.
     */
    public function getAnnualSavingsPercentAttribute(): int
    {
        if ($this->monthly_price <= 0) {
            return 0;
        }
        $monthlyTotal = $this->monthly_price * 12;
        return (int) round((1 - ($this->annual_price / $monthlyTotal)) * 100);
    }

    /**
     * Get Stripe price ID for a billing cycle.
     */
    public function getStripePriceId(string $billingCycle): ?string
    {
        return match ($billingCycle) {
            'monthly' => $this->stripe_monthly_price_id,
            'quarterly' => $this->stripe_quarterly_price_id,
            'annual' => $this->stripe_annual_price_id,
            default => null,
        };
    }

    /**
     * Get price for a billing cycle (in cents).
     */
    public function getPrice(string $billingCycle): int
    {
        return match ($billingCycle) {
            'monthly' => $this->monthly_price,
            'quarterly' => $this->quarterly_price,
            'annual' => $this->annual_price,
            default => 0,
        };
    }

    /**
     * Get a specific limit value.
     */
    public function getLimit(string $key, mixed $default = null): mixed
    {
        return $this->limits[$key] ?? $default;
    }

    /**
     * Check if plan has a specific feature enabled.
     */
    public function hasFeature(string $key): bool
    {
        $limit = $this->getLimit($key);

        if (is_bool($limit)) {
            return $limit;
        }

        if (is_null($limit)) {
            return true; // null means unlimited/enabled
        }

        if (is_numeric($limit)) {
            return $limit > 0;
        }

        return (bool) $limit;
    }

    /**
     * Scope to active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get default limits structure.
     */
    public static function getDefaultLimits(): array
    {
        return [
            'max_properties' => 10,
            'max_blog_posts' => 5,
            'max_pages' => 3,
            'max_testimonials' => 10,
            'max_images_per_property' => 5,
            'can_use_custom_domain' => false,
            'can_access_analytics' => false,
            'can_remove_branding' => false,
            'templates_access' => ['basic'],
            'priority_support' => false,
        ];
    }
}
