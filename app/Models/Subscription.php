<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'tenant_id',
        'plan_id',
        'stripe_subscription_id',
        'stripe_price_id',
        'billing_cycle',
        'quantity',
        'status',
        'current_period_start',
        'current_period_end',
        'trial_ends_at',
        'ends_at',
        'canceled_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'current_period_start' => 'datetime',
            'current_period_end' => 'datetime',
            'trial_ends_at' => 'datetime',
            'ends_at' => 'datetime',
            'canceled_at' => 'datetime',
            'quantity' => 'integer',
        ];
    }

    /**
     * Billing cycle options.
     */
    public const BILLING_CYCLES = [
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'annual' => 'Annual',
    ];

    /**
     * Status options.
     */
    public const STATUSES = [
        'incomplete' => 'Incomplete',
        'incomplete_expired' => 'Incomplete Expired',
        'trialing' => 'Trialing',
        'active' => 'Active',
        'past_due' => 'Past Due',
        'canceled' => 'Canceled',
        'unpaid' => 'Unpaid',
    ];

    /**
     * Get the tenant that owns the subscription.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user that owns the subscription (deprecated).
     * @deprecated Use tenant() instead
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    /**
     * Get the plan for this subscription.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Check if subscription is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if subscription is past due.
     */
    public function isPastDue(): bool
    {
        return $this->status === 'past_due';
    }

    /**
     * Check if subscription is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'canceled';
    }

    /**
     * Check if subscription is on trial.
     */
    public function onTrial(): bool
    {
        return $this->status === 'trialing' ||
            ($this->trial_ends_at && $this->trial_ends_at->isFuture());
    }

    /**
     * Check if subscription has valid access.
     */
    public function hasValidAccess(): bool
    {
        return in_array($this->status, ['active', 'trialing', 'past_due']);
    }

    /**
     * Check if subscription is scheduled for cancellation.
     */
    public function onGracePeriod(): bool
    {
        return $this->canceled_at !== null && $this->ends_at?->isFuture();
    }

    /**
     * Get days remaining in current period.
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->current_period_end) {
            return null;
        }

        return (int) now()->diffInDays($this->current_period_end, false);
    }

    /**
     * Get formatted billing cycle.
     */
    public function getFormattedBillingCycleAttribute(): string
    {
        return self::BILLING_CYCLES[$this->billing_cycle] ?? ucfirst($this->billing_cycle ?? 'monthly');
    }

    /**
     * Get a limit from the associated plan.
     */
    public function getLimit(string $key, mixed $default = null): mixed
    {
        return $this->plan?->getLimit($key, $default) ?? $default;
    }

    /**
     * Check if subscription plan has a specific feature.
     */
    public function hasFeature(string $key): bool
    {
        return $this->plan?->hasFeature($key) ?? false;
    }

    /**
     * Scope for active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for valid subscriptions (can access features).
     */
    public function scopeValid($query)
    {
        return $query->whereIn('status', ['active', 'trialing', 'past_due']);
    }
}
