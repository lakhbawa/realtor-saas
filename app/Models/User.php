<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'is_admin',
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
        ];
    }

    /**
     * Determine if the user can access the Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            // Super admins always have access
            return $this->is_admin;
        }

        // Tenant panel - check if user belongs to at least one tenant with valid subscription
        return $this->tenants()->exists() && $this->hasValidSubscriptionInAnyTenant();
    }

    /**
     * Check if user has a valid subscription in any of their tenants.
     */
    public function hasValidSubscriptionInAnyTenant(): bool
    {
        return $this->tenants()
            ->whereIn('subscription_status', ['active', 'trialing', 'past_due'])
            ->exists();
    }

    /**
     * Check if user has a valid subscription (for backwards compatibility).
     * Uses the current tenant context if available.
     */
    public function hasValidSubscription(): bool
    {
        $currentTenant = $this->currentTenant();

        if ($currentTenant) {
            return $currentTenant->hasValidSubscription();
        }

        return $this->hasValidSubscriptionInAnyTenant();
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
     * Get all tenants the user belongs to.
     */
    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_user')
            ->withPivot('role')
            ->withTimestamps()
            ->using(TenantUser::class);
    }

    /**
     * Get the current tenant from context.
     */
    public function currentTenant(): ?Tenant
    {
        return app('currentTenant');
    }

    /**
     * Set the current tenant context.
     */
    public function setCurrentTenant(?Tenant $tenant): void
    {
        app()->instance('currentTenant', $tenant);
    }

    /**
     * Get tenants where user is an owner.
     */
    public function ownedTenants(): BelongsToMany
    {
        return $this->tenants()->wherePivot('role', TenantUser::ROLE_OWNER);
    }

    /**
     * Check if user is a member of a specific tenant.
     */
    public function belongsToTenant(Tenant $tenant): bool
    {
        return $this->tenants()->where('tenant_id', $tenant->id)->exists();
    }

    /**
     * Get user's role in a specific tenant.
     */
    public function roleInTenant(Tenant $tenant): ?string
    {
        $pivot = $this->tenants()->where('tenant_id', $tenant->id)->first()?->pivot;
        return $pivot?->role;
    }

    /**
     * Check if user has a specific role in a tenant.
     */
    public function hasRoleInTenant(Tenant $tenant, string $role): bool
    {
        return $this->roleInTenant($tenant) === $role;
    }

    /**
     * Check if user can manage a specific tenant.
     */
    public function canManageTenant(Tenant $tenant): bool
    {
        if ($this->is_admin) {
            return true;
        }

        $role = $this->roleInTenant($tenant);
        return in_array($role, [TenantUser::ROLE_OWNER, TenantUser::ROLE_ADMIN]);
    }

    /**
     * Check if user can edit content in a specific tenant.
     */
    public function canEditInTenant(Tenant $tenant): bool
    {
        if ($this->is_admin) {
            return true;
        }

        $role = $this->roleInTenant($tenant);
        return in_array($role, [
            TenantUser::ROLE_OWNER,
            TenantUser::ROLE_ADMIN,
            TenantUser::ROLE_EDITOR,
        ]);
    }

    /**
     * Get the site for the user (deprecated - use tenant->site instead).
     * @deprecated Use currentTenant()->site instead
     */
    public function site(): HasOne
    {
        return $this->hasOne(Site::class);
    }

    /**
     * Get the subscription for the user (deprecated - use tenant->subscription instead).
     * @deprecated Use currentTenant()->subscription instead
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    /**
     * Get all properties for the user (deprecated - use tenant->properties instead).
     * @deprecated Use currentTenant()->properties instead
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'created_by');
    }

    /**
     * Get all testimonials for the user (deprecated - use tenant->testimonials instead).
     * @deprecated Use currentTenant()->testimonials instead
     */
    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class, 'created_by');
    }

    /**
     * Get all contact submissions for the user (deprecated).
     * @deprecated Use currentTenant()->contactSubmissions instead
     */
    public function contactSubmissions(): HasMany
    {
        return $this->hasMany(ContactSubmission::class);
    }

    /**
     * Get all pages for the user (deprecated - use tenant->pages instead).
     * @deprecated Use currentTenant()->pages instead
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class, 'created_by');
    }

    /**
     * Get all blog posts for the user (deprecated - use tenant->blogPosts instead).
     * @deprecated Use currentTenant()->blogPosts instead
     */
    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'created_by');
    }

    /**
     * Check if user has an active subscription.
     * Uses current tenant context if available.
     */
    public function hasActiveSubscription(): bool
    {
        $currentTenant = $this->currentTenant();

        if ($currentTenant) {
            return $currentTenant->hasActiveSubscription();
        }

        return $this->tenants()
            ->where('subscription_status', 'active')
            ->exists();
    }

    /**
     * Check if user's subscription is past due.
     * Uses current tenant context if available.
     */
    public function isPastDue(): bool
    {
        $currentTenant = $this->currentTenant();

        if ($currentTenant) {
            return $currentTenant->subscription_status === 'past_due';
        }

        return false;
    }

    /**
     * Check if user is on trial.
     * Uses current tenant context if available.
     */
    public function onTrial(): bool
    {
        $currentTenant = $this->currentTenant();

        if ($currentTenant) {
            return $currentTenant->onTrial();
        }

        return false;
    }

    /**
     * Get the public URL for the tenant's site.
     * Uses current tenant context if available.
     */
    public function getPublicUrlAttribute(): string
    {
        $currentTenant = $this->currentTenant();

        if ($currentTenant) {
            return $currentTenant->url();
        }

        // Fallback for backwards compatibility
        $firstTenant = $this->tenants()->first();
        return $firstTenant ? $firstTenant->url() : '';
    }

    /**
     * Get a plan limit value.
     * Uses current tenant context if available.
     */
    public function getPlanLimit(string $key, mixed $default = null): mixed
    {
        if ($this->is_admin) {
            return -1; // Unlimited for admins
        }

        $currentTenant = $this->currentTenant();

        if ($currentTenant) {
            return $currentTenant->getPlanLimit($key, $default);
        }

        return $default;
    }

    /**
     * Check if user's plan has a specific feature.
     * Uses current tenant context if available.
     */
    public function hasPlanFeature(string $key): bool
    {
        if ($this->is_admin) {
            return true;
        }

        $currentTenant = $this->currentTenant();

        if ($currentTenant) {
            return $currentTenant->hasAccessToFeature($key);
        }

        return false;
    }

    /**
     * Check if user can create more of a resource.
     * Uses current tenant context if available.
     */
    public function canCreateMore(string $limitKey, string $relation): bool
    {
        if ($this->is_admin) {
            return true;
        }

        $currentTenant = $this->currentTenant();

        if ($currentTenant) {
            // Convert limitKey like 'max_properties' to resource type 'properties'
            $resourceType = str_replace('max_', '', $limitKey);
            return $currentTenant->canCreateMore($resourceType);
        }

        return false;
    }

    /**
     * Get remaining quota for a resource.
     * Uses current tenant context if available.
     */
    public function getRemainingQuota(string $limitKey, string $relation): int|string|null
    {
        if ($this->is_admin) {
            return 'unlimited';
        }

        $currentTenant = $this->currentTenant();

        if ($currentTenant) {
            // Convert limitKey like 'max_properties' to resource type 'properties'
            $resourceType = str_replace('max_', '', $limitKey);
            return $currentTenant->getRemainingQuota($resourceType);
        }

        return 0;
    }

    /**
     * Get the current plan.
     * Uses current tenant context if available.
     */
    public function getCurrentPlan(): ?\App\Models\Plan
    {
        $currentTenant = $this->currentTenant();

        if ($currentTenant) {
            return $currentTenant->subscription?->plan;
        }

        return null;
    }
}
