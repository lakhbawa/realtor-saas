<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'subdomain',
        'is_admin',
        'stripe_customer_id',
        'stripe_subscription_id',
        'subscription_status',
        'trial_ends_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'trial_ends_at' => 'datetime',
        ];
    }

    /**
     * Determine if the user can access the Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            // Super admins always have access
            if ($this->is_admin) {
                return true;
            }

            // Tenant users with active subscription can access admin panel
            // They will see only tenant-relevant resources
            return $this->hasValidSubscription();
        }

        // Tenant panel - check subscription status
        return $this->hasValidSubscription();
    }

    /**
     * Check if user has a valid subscription (active, trialing, or past_due).
     */
    public function hasValidSubscription(): bool
    {
        return in_array($this->subscription_status, ['active', 'trialing', 'past_due']);
    }

    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * Check if user is a tenant (non-admin user).
     */
    public function isTenant(): bool
    {
        return !$this->is_admin;
    }

    /**
     * Get the site for the user.
     */
    public function site(): HasOne
    {
        return $this->hasOne(Site::class);
    }

    /**
     * Get the subscription for the user.
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    /**
     * Get all properties for the user.
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Get all testimonials for the user.
     */
    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    /**
     * Get all contact submissions for the user.
     */
    public function contactSubmissions(): HasMany
    {
        return $this->hasMany(ContactSubmission::class);
    }

    /**
     * Get all pages for the user.
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    /**
     * Get all blog posts for the user.
     */
    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    /**
     * Check if user has an active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscription_status === 'active';
    }

    /**
     * Check if user's subscription is past due.
     */
    public function isPastDue(): bool
    {
        return $this->subscription_status === 'past_due';
    }

    /**
     * Check if user is on trial.
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Get the public URL for the tenant's site.
     */
    public function getPublicUrlAttribute(): string
    {
        $domain = config('app.domain', 'myrealtorsites.local');
        return "http://{$this->subdomain}.{$domain}";
    }

    /**
     * Get a plan limit value.
     */
    public function getPlanLimit(string $key, mixed $default = null): mixed
    {
        return $this->subscription?->getLimit($key, $default) ?? $default;
    }

    /**
     * Check if user's plan has a specific feature.
     */
    public function hasPlanFeature(string $key): bool
    {
        if ($this->is_admin) {
            return true;
        }

        return $this->subscription?->hasFeature($key) ?? false;
    }

    /**
     * Check if user can create more of a resource.
     */
    public function canCreateMore(string $limitKey, string $relation): bool
    {
        if ($this->is_admin) {
            return true;
        }

        $limit = $this->getPlanLimit($limitKey);

        // null or -1 means unlimited
        if ($limit === null || $limit === -1) {
            return true;
        }

        return $this->$relation()->count() < $limit;
    }

    /**
     * Get remaining quota for a resource.
     */
    public function getRemainingQuota(string $limitKey, string $relation): ?int
    {
        if ($this->is_admin) {
            return null; // unlimited
        }

        $limit = $this->getPlanLimit($limitKey);

        // null or -1 means unlimited
        if ($limit === null || $limit === -1) {
            return null;
        }

        return max(0, $limit - $this->$relation()->count());
    }

    /**
     * Get the current plan.
     */
    public function getCurrentPlan(): ?\App\Models\Plan
    {
        return $this->subscription?->plan;
    }
}
