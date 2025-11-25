# Multi-Tenant Architecture Documentation

## Overview

This document describes the **scalable multi-tenant architecture** implemented for the Realtor SaaS platform. The architecture has been redesigned to properly separate tenants (organizations/realtor companies) from users (team members), enabling team collaboration and proper data isolation.

## Critical Architectural Changes

### ❌ Old Architecture (Problems)

```
User (subdomain, subscription)
  └─ 1:1 Site
  └─ 1:N Properties, Pages, BlogPosts, etc.
```

**Problems:**
- Subdomain was tied directly to User
- One user = One site (no ability to have multiple sites)
- No team collaboration (multiple users can't work on same site)
- Subscription tied to individual user, not organization
- Not scalable for team features

### ✅ New Architecture (Scalable)

```
Tenant (Organization/Company)
  ├─ subscription/billing
  ├─ M:N Users (team members with roles)
  ├─ 1:N Sites (each with unique subdomain)
  └─ 1:N Properties, Pages, BlogPosts, etc.

Site
  ├─ subdomain (unique)
  ├─ belongsTo Tenant
  └─ website configuration

User (Authentication)
  └─ M:N Tenants (can belong to multiple organizations)
```

**Benefits:**
- Subdomain tied to Site (allows multiple sites per tenant)
- Multiple users can collaborate on same tenant
- One tenant can manage multiple branded sites/subdomains
- Role-based access control (owner, admin, editor, member, viewer)
- Team billing (subscription belongs to tenant)
- Users can be members of multiple tenants
- Perfect for agencies managing multiple client sites
- Scalable for enterprise features

---

## Database Schema

### Core Tables

#### `tenants`
The primary entity representing an organization/realtor company.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `name` | string | Company/Organization name |
| `stripe_customer_id` | string | Stripe customer ID |
| `stripe_subscription_id` | string | Stripe subscription ID |
| `subscription_status` | string | active, trialing, past_due, etc. |
| `trial_ends_at` | timestamp | Trial expiration |
| `deleted_at` | timestamp | Soft delete |

#### `tenant_user` (Pivot)
Many-to-many relationship between tenants and users with roles.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `tenant_id` | foreignId | References tenants.id |
| `user_id` | foreignId | References users.id |
| `role` | string | owner, admin, editor, member, viewer |
| Unique: | `(tenant_id, user_id)` | One role per user per tenant |

#### `users`
Authentication entity representing individual users.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `name` | string | User's name |
| `email` | string (unique) | Email address |
| `password` | string | Hashed password |
| `is_admin` | boolean | Super admin flag |
| **Removed:** | `subdomain` | ❌ Moved to sites |
| **Removed:** | `stripe_*` | ❌ Moved to tenants |
| **Removed:** | `subscription_status` | ❌ Moved to tenants |

### Content Tables

All content tables now reference `tenant_id` instead of `user_id`:

#### `sites`
| Column | Type | Description |
|--------|------|-------------|
| `tenant_id` | foreignId (indexed) | References tenants.id |
| `subdomain` | string (unique) | Subdomain identifier |
| `custom_domain` | string (unique, nullable) | Custom domain (e.g., www.johndoe.com) |
| `custom_domain_verified` | boolean | Whether custom domain is verified |
| `updated_by` | foreignId | Last user who updated |
| `template_id` | foreignId | Template used |
| ... | | Site configuration fields |

**Note:** One tenant can have multiple sites. Each site has its own unique subdomain and can optionally have a verified custom domain.

#### `properties`
| Column | Type | Description |
|--------|------|-------------|
| `tenant_id` | foreignId | References tenants.id |
| `created_by` | foreignId | User who created |
| `updated_by` | foreignId | User who last updated |
| ... | | Property details |

**Similar structure for:**
- `pages`
- `blog_posts`
- `testimonials`
- `contact_submissions`

#### `subscriptions`
| Column | Type | Description |
|--------|------|-------------|
| `tenant_id` | foreignId | References tenants.id |
| `plan_id` | foreignId | References plans.id |
| ... | | Subscription details |

---

## Models

### Tenant Model

**Location:** `app/Models/Tenant.php`

**Key Relationships:**
```php
$tenant->users()              // All team members (BelongsToMany)
$tenant->owners()             // Team members with 'owner' role
$tenant->sites()              // All websites (HasMany) - NEW!
$tenant->site()               // Primary/first site (HasOne) - for backwards compatibility
$tenant->subscription()       // Billing subscription (HasOne)
$tenant->properties()         // Real estate listings (HasMany)
$tenant->pages()              // Custom pages (HasMany)
$tenant->blogPosts()          // Blog content (HasMany)
$tenant->testimonials()       // Client testimonials (HasMany)
$tenant->contactSubmissions() // Contact form submissions (HasMany)
```

**Key Methods:**
```php
$tenant->hasValidSubscription()           // Check if subscription is valid
$tenant->hasActiveSubscription()          // Check if status is 'active'
$tenant->onTrial()                        // Check if on trial period
$tenant->url()                            // Get primary site URL (deprecated)
$tenant->hasAccessToFeature($feature)     // Check plan feature access
$tenant->getPlanLimit($limit)             // Get plan limit value
$tenant->canCreateMore($resourceType)     // Check quota before creation
$tenant->getRemainingQuota($resourceType) // Get remaining quota
$tenant->hasMember($user)                 // Check if user is a member
$tenant->userHasRole($user, $role)        // Check user's role
$tenant->addUser($user, $role)            // Add user to tenant
$tenant->removeUser($user)                // Remove user from tenant
$tenant->updateUserRole($user, $role)     // Update user's role
```

### User Model

**Location:** `app/Models/User.php`

**Key Relationships:**
```php
$user->tenants()       // All tenants user belongs to (BelongsToMany)
$user->ownedTenants()  // Tenants where user is owner
$user->currentTenant() // Get current tenant from context
```

**Key Methods:**
```php
// Tenant Context
$user->setCurrentTenant($tenant)         // Set current tenant context
$user->belongsToTenant($tenant)          // Check membership
$user->roleInTenant($tenant)             // Get role in tenant
$user->hasRoleInTenant($tenant, $role)   // Check specific role
$user->canManageTenant($tenant)          // Check management permissions
$user->canEditInTenant($tenant)          // Check edit permissions

// Subscription Methods (use current tenant context)
$user->hasValidSubscription()            // Checks current tenant
$user->hasActiveSubscription()           // Checks current tenant
$user->onTrial()                         // Checks current tenant
$user->getPlanLimit($key)                // Gets from current tenant
$user->hasPlanFeature($key)              // Checks current tenant
$user->canCreateMore($limitKey, $rel)    // Checks current tenant
```

### TenantUser Model (Pivot)

**Location:** `app/Models/TenantUser.php`

**Available Roles:**
- `owner` - Full control, can manage billing and team
- `admin` - Can manage content and team members
- `editor` - Can create and edit content
- `member` - Can view and comment
- `viewer` - Read-only access

**Methods:**
```php
$tenantUser->isOwner()   // Check if role is owner
$tenantUser->isAdmin()   // Check if role is admin
$tenantUser->canEdit()   // Check if can edit content
$tenantUser->canManage() // Check if can manage settings
```

---

## Tenant Resolution (Middleware)

**Location:** `app/Http/Middleware/TenantMiddleware.php`

### How It Works

1. **Try Custom Domain First** (exact match on host)
   ```php
   $site = Site::where('custom_domain', $host)
       ->where('custom_domain_verified', true)
       ->with('tenant')
       ->first();
   ```
   ```
   Request: https://www.johndoe.com
   Matches: custom_domain = 'www.johndoe.com'
   ```

2. **Fallback to Subdomain** if no custom domain match
   ```
   Request: https://johndoe.myrealtorsites.com
   Subdomain: johndoe
   ```
   ```php
   $site = Site::where('subdomain', 'johndoe')
       ->with('tenant')
       ->first();
   ```

3. **Get Tenant** from site
   ```php
   $tenant = $site->tenant;
   ```

4. **Validate Subscription** status
   ```php
   if (!$tenant->hasValidSubscription()) {
       abort(403, 'Site unavailable');
   }
   ```

5. **Bind to Container** for global access
   ```php
   app()->instance('site', $site);
   app()->instance('tenant', $tenant);
   app()->instance('currentTenant', $tenant);
   app()->instance('currentSite', $site);
   ```

6. **Set User Context** if authenticated
   ```php
   auth()->user()->setCurrentTenant($tenant);
   ```

### Reserved Subdomains

The following subdomains are reserved and will not be resolved:
- `www`, `admin`, `api`, `app`, `mail`, `ftp`, `dashboard`

### Accessing Current Tenant & Site

```php
// In Controllers
$tenant = app('currentTenant');
$tenant = request()->get('tenant');
$tenant = auth()->user()->currentTenant();

$site = app('currentSite');
$site = request()->get('site');

// In Views
{{ $tenant->name }}
{{ $site->subdomain }}
{{ $site->url() }}

// In Models (via global scopes)
// Automatically filtered by tenant_id
```

---

## Global Scopes (Automatic Tenant Filtering)

All content models use global scopes to automatically filter by current tenant:

**Example: Property Model**

```php
protected static function boot()
{
    parent::boot();

    // Auto-filter by current tenant
    static::addGlobalScope('tenant', function (Builder $builder) {
        if (auth()->check() && !auth()->user()->is_admin) {
            $currentTenant = auth()->user()->currentTenant();
            if ($currentTenant) {
                $builder->where('tenant_id', $currentTenant->id);
            }
        }
    });

    // Auto-set tenant_id on create
    static::creating(function ($property) {
        if (!$property->tenant_id && auth()->check()) {
            $currentTenant = auth()->user()->currentTenant();
            if ($currentTenant) {
                $property->tenant_id = $currentTenant->id;
            }
        }
    });
}
```

**Applied to:**
- Property
- Page
- BlogPost
- Testimonial
- ContactSubmission

---

## Admin vs Tenant Panels

### Admin Panel (`/admin`)
- **Access:** Super admins only (`is_admin = true`)
- **Resources:** Manage all tenants, users, subscriptions, plans
- **Scope:** Global - can see all data

### Tenant Panel (`/dashboard`)
- **Access:** Tenant team members with valid subscription
- **Resources:** Properties, Pages, BlogPosts, Testimonials, etc.
- **Scope:** Scoped to current tenant only

---

## User Flows

### New Tenant Registration

1. User creates account and chooses subdomain
2. System creates:
   - User record
   - Tenant record (with subdomain)
   - TenantUser pivot (role: 'owner')
   - Site record (linked to tenant)
3. User becomes owner of new tenant

### Inviting Team Members

1. Owner/Admin invites user by email
2. If user doesn't exist, create User record
3. Create TenantUser pivot with specified role
4. User can now access tenant with assigned role

### Accessing Multiple Tenants

1. User logs in
2. System shows list of their tenants
3. User selects tenant to work on
4. Current tenant set in session/context
5. All queries automatically scoped to that tenant

---

## Migration Path

### Running Migrations

```bash
# Run all new migrations
php artisan migrate

# This will:
# 1. Create tenants table
# 2. Create tenant_user pivot table
# 3. Remove subdomain from users table
# 4. Update all content tables to use tenant_id
```

### Data Migration Strategy

**For existing production data:**

1. **Create Tenant for each User:**
   ```php
   foreach (User::where('is_admin', false)->get() as $user) {
       $tenant = Tenant::create([
           'name' => $user->name . "'s Company",
           'subdomain' => $user->subdomain,
           'stripe_customer_id' => $user->stripe_customer_id,
           'subscription_status' => $user->subscription_status,
       ]);

       // Link user as owner
       $tenant->addUser($user, 'owner');
   }
   ```

2. **Migrate content to tenants:**
   ```php
   foreach (Property::all() as $property) {
       $user = User::find($property->user_id);
       $tenant = $user->tenants()->first();

       $property->update([
           'tenant_id' => $tenant->id,
           'created_by' => $user->id,
       ]);
   }
   ```

3. **Similar migration for:**
   - Sites
   - Pages
   - BlogPosts
   - Testimonials
   - ContactSubmissions
   - Subscriptions

---

## Security & Isolation

### Database Level
- Foreign key constraints ensure referential integrity
- `ON DELETE CASCADE` removes orphaned records

### Query Level
- Global scopes automatically filter by `tenant_id`
- Admins bypass scopes for management purposes

### URL Level
- Subdomain-based routing ensures URL isolation
- TenantMiddleware validates subscription before access

### Authorization Level
- Role-based permissions via TenantUser pivot
- Filament resources check `canAccess()` based on role

### Subscription Level
- Valid subscription required for tenant access
- Status checked on every request via middleware

---

## API Reference

### Site Methods

```php
// Site instance - resolved by subdomain or custom domain
$site = Site::where('subdomain', 'johndoe')->first();

// Relationships
$site->tenant()      // Tenant that owns this site
$site->updatedBy()   // User who last updated
$site->template()    // Template used

// URL Methods
$site->url()                 // Returns custom domain if verified, otherwise subdomain URL
                             // https://www.johndoe.com OR https://johndoe.myrealtorsites.com
$site->subdomainUrl()        // Always returns subdomain URL
                             // https://johndoe.myrealtorsites.com
$site->customDomainUrl()     // Returns custom domain URL or null
                             // https://www.johndoe.com OR null

// Custom Domain Checks
$site->hasCustomDomain()                  // bool - true if custom domain is set AND verified
$site->customDomainPendingVerification()  // bool - true if custom domain set but not verified

// Configuration
$site->isSetupComplete()             // bool
$site->getSetupProgressAttribute()   // int (0-100)
```

### Tenant Methods

```php
// Tenant instance
$tenant = Tenant::find($id);
// OR via site
$tenant = $site->tenant;

// Sites
$tenant->sites()     // All sites (Collection)
$tenant->site()      // Primary/first site

// Subscription
$tenant->hasValidSubscription()     // bool
$tenant->hasActiveSubscription()    // bool
$tenant->onTrial()                  // bool

// Features & Limits
$tenant->hasAccessToFeature('can_use_custom_domain')  // bool
$tenant->getPlanLimit('max_properties')               // int
$tenant->canCreateMore('properties')                  // bool
$tenant->getRemainingQuota('properties')              // int|string

// Team Management
$tenant->hasMember($user)                    // bool
$tenant->userHasRole($user, 'admin')         // bool
$tenant->addUser($user, 'editor')            // void
$tenant->removeUser($user)                   // void
$tenant->updateUserRole($user, 'admin')      // void

// URLs (deprecated - use $site->url() instead)
$tenant->url()  // Primary site URL (deprecated)
```

### User Methods

```php
// User instance
$user = auth()->user();

// Tenant Membership
$user->tenants()                    // Collection of Tenants
$user->ownedTenants()              // Tenants where role=owner
$user->currentTenant()              // Current Tenant|null
$user->belongsToTenant($tenant)     // bool

// Roles
$user->roleInTenant($tenant)                    // string|null
$user->hasRoleInTenant($tenant, 'admin')       // bool
$user->canManageTenant($tenant)                // bool
$user->canEditInTenant($tenant)                // bool

// Context
$user->setCurrentTenant($tenant)   // void
```

---

## Best Practices

### 1. Always Use Current Tenant Context

```php
// ✅ Good
$tenant = auth()->user()->currentTenant();
$properties = $tenant->properties;

// ❌ Bad
$properties = Property::all(); // May leak data
```

### 2. Check Permissions Before Actions

```php
// ✅ Good
if ($user->canManageTenant($tenant)) {
    $tenant->updateUserRole($member, 'admin');
}

// ❌ Bad
$tenant->updateUserRole($member, 'admin'); // No permission check
```

### 3. Validate Quotas Before Creation

```php
// ✅ Good
if (!$tenant->canCreateMore('properties')) {
    return back()->withErrors('Property limit reached');
}

// ❌ Bad
Property::create([...]); // May exceed quota
```

### 4. Use Tenant-Scoped Queries

```php
// ✅ Good
$tenant->properties()->where('status', 'active')->get();

// ❌ Bad
Property::where('tenant_id', $tenant->id)
    ->where('status', 'active')->get();
```

---

## Testing

### Unit Tests

```php
// Test tenant creation
$tenant = Tenant::factory()->create();
$this->assertNotNull($tenant->subdomain);

// Test user membership
$user = User::factory()->create();
$tenant->addUser($user, 'editor');
$this->assertTrue($tenant->hasMember($user));

// Test permissions
$this->assertTrue($user->canEditInTenant($tenant));
$this->assertFalse($user->canManageTenant($tenant));
```

### Feature Tests

```php
// Test subdomain routing
$response = $this->get('https://johndoe.myrealtorsites.com');
$response->assertStatus(200);

// Test tenant isolation
$tenant1 = Tenant::factory()->create();
$tenant2 = Tenant::factory()->create();
$property = Property::factory()->create(['tenant_id' => $tenant1->id]);

$this->actingAs($tenant2->users->first());
$this->assertNull(Property::find($property->id)); // Scoped out
```

---

## Troubleshooting

### Issue: "Site not found" error

**Cause:** Subdomain doesn't match any tenant
**Solution:** Verify subdomain is correct and tenant exists

### Issue: Properties not showing up

**Cause:** Current tenant context not set
**Solution:** Ensure TenantMiddleware is running and setting context

### Issue: Can't access tenant after login

**Cause:** User not member of tenant or subscription invalid
**Solution:** Check `tenant_user` pivot and `subscription_status`

### Issue: Wrong data showing up

**Cause:** Current tenant context is wrong
**Solution:** Verify `auth()->user()->currentTenant()` returns correct tenant

---

## Custom Domain Support

✅ **Implemented** - Sites can use custom domains in addition to subdomains.

### Features

- Each site can have one custom domain (e.g., www.johndoe.com)
- Custom domains must be verified before use
- URL resolution prioritizes custom domain over subdomain
- Subdomain remains as fallback if custom domain is removed

### Implementation

**Database Fields (sites table):**
- `custom_domain` - Stores the custom domain (unique, nullable)
- `custom_domain_verified` - Boolean flag for verification status

**DNS Setup Required:**
Tenants need to add a CNAME record pointing to their subdomain:
```
CNAME: www.johndoe.com → johndoe.myrealtorsites.com
```

**Verification Workflow:**
1. Tenant enters custom domain in dashboard
2. System sets `custom_domain` but `custom_domain_verified = false`
3. Tenant configures DNS CNAME record
4. System verifies DNS propagation (manual or automated)
5. System sets `custom_domain_verified = true`
6. Site becomes accessible via custom domain

**Future Enhancement:**
If multiple domains per site are needed, migrate to separate `domains` table with many-to-one relationship to sites.

---

## Future Enhancements

1. **Multi-Site Per Tenant** ✅ **Completed**
   - One tenant can now manage multiple sites/subdomains
   - Useful for agencies managing client sites

2. **Multiple Custom Domains Per Site**
   - Currently limited to one custom domain per site
   - Could migrate to `domains` table for unlimited domains
   - Requires: domains table with site_id foreign key

3. **Automated Custom Domain Verification**
   - Automatically verify DNS CNAME records
   - Use DNS lookup APIs to check propagation
   - Email notifications when verification succeeds/fails

4. **Team Invitations**
   - Email invites with role assignment
   - Accept/decline flow
   - Invitation expiration

5. **Audit Logging**
   - Track who created/updated what
   - Use `created_by` and `updated_by` fields
   - Activity timeline in dashboard

6. **Role Permissions**
   - Fine-grained permissions per role
   - Permission matrix in database
   - Customizable permissions per tenant

---

## Summary

The new multi-tenant architecture provides:

✅ **Proper tenant isolation** (subdomain → Tenant, not User)
✅ **Team collaboration** (multiple users per tenant)
✅ **Role-based access control** (owner, admin, editor, etc.)
✅ **Scalable billing** (subscription per tenant, not user)
✅ **Better data organization** (tenant_id on all content)
✅ **Security** (global scopes, middleware validation)
✅ **Flexibility** (users can belong to multiple tenants)

This architecture supports growth from single-user realtors to large real estate agencies with teams.
