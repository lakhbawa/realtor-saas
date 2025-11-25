<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'subdomain',
        'stripe_customer_id',
        'stripe_subscription_id',
        'subscription_status',
        'trial_ends_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
        ];
    }

    /**
     * Get the site for the tenant.
     */
    public function site(): HasOne
    {
        return $this->hasOne(Site::class);
    }

    /**
     * Get the subscription for the tenant.
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    /**
     * Get all users that belong to this tenant.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tenant_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the owner(s) of the tenant.
     */
    public function owners(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'owner');
    }

    /**
     * Get the properties for the tenant.
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Get the pages for the tenant.
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    /**
     * Get the blog posts for the tenant.
     */
    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    /**
     * Get the testimonials for the tenant.
     */
    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    /**
     * Get the contact submissions for the tenant.
     */
    public function contactSubmissions(): HasMany
    {
        return $this->hasMany(ContactSubmission::class);
    }

    /**
     * Check if the tenant has a valid subscription.
     */
    public function hasValidSubscription(): bool
    {
        return in_array($this->subscription_status, ['active', 'trialing', 'past_due']);
    }

    /**
     * Check if the tenant has an active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscription_status === 'active';
    }

    /**
     * Check if the tenant is on trial.
     */
    public function onTrial(): bool
    {
        return $this->subscription_status === 'trialing' &&
               $this->trial_ends_at &&
               $this->trial_ends_at->isFuture();
    }

    /**
     * Get the tenant's public URL.
     */
    public function url(): string
    {
        $domain = config('app.base_domain', config('app.url'));
        return "https://{$this->subdomain}.{$domain}";
    }

    /**
     * Check if the tenant has access to a specific feature.
     */
    public function hasAccessToFeature(string $feature): bool
    {
        if (!$this->hasValidSubscription()) {
            return false;
        }

        $subscription = $this->subscription;
        if (!$subscription || !$subscription->plan) {
            return false;
        }

        return $subscription->plan->hasFeature($feature);
    }

    /**
     * Get a plan limit for the tenant.
     */
    public function getPlanLimit(string $limit, $default = null)
    {
        $subscription = $this->subscription;
        if (!$subscription || !$subscription->plan) {
            return $default;
        }

        return $subscription->plan->getLimit($limit, $default);
    }

    /**
     * Check if the tenant can create more of a resource type.
     */
    public function canCreateMore(string $resourceType): bool
    {
        $limitKey = "max_{$resourceType}";
        $limit = $this->getPlanLimit($limitKey);

        if ($limit === null || $limit === -1) {
            return true; // Unlimited
        }

        $currentCount = match($resourceType) {
            'properties' => $this->properties()->count(),
            'blog_posts' => $this->blogPosts()->count(),
            'pages' => $this->pages()->count(),
            'testimonials' => $this->testimonials()->count(),
            default => 0,
        };

        return $currentCount < $limit;
    }

    /**
     * Get remaining quota for a resource type.
     */
    public function getRemainingQuota(string $resourceType): int|string
    {
        $limitKey = "max_{$resourceType}";
        $limit = $this->getPlanLimit($limitKey);

        if ($limit === null || $limit === -1) {
            return 'unlimited';
        }

        $currentCount = match($resourceType) {
            'properties' => $this->properties()->count(),
            'blog_posts' => $this->blogPosts()->count(),
            'pages' => $this->pages()->count(),
            'testimonials' => $this->testimonials()->count(),
            default => 0,
        };

        return max(0, $limit - $currentCount);
    }

    /**
     * Check if a user is a member of this tenant.
     */
    public function hasMember(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if a user has a specific role in this tenant.
     */
    public function userHasRole(User $user, string $role): bool
    {
        return $this->users()
            ->where('user_id', $user->id)
            ->wherePivot('role', $role)
            ->exists();
    }

    /**
     * Add a user to this tenant with a specific role.
     */
    public function addUser(User $user, string $role = 'member'): void
    {
        if (!$this->hasMember($user)) {
            $this->users()->attach($user->id, ['role' => $role]);
        }
    }

    /**
     * Remove a user from this tenant.
     */
    public function removeUser(User $user): void
    {
        $this->users()->detach($user->id);
    }

    /**
     * Update a user's role in this tenant.
     */
    public function updateUserRole(User $user, string $role): void
    {
        $this->users()->updateExistingPivot($user->id, ['role' => $role]);
    }
}
