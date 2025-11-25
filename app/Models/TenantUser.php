<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TenantUser extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tenant_user';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'tenant_id',
        'user_id',
        'role',
    ];

    /**
     * Available roles in the system.
     */
    public const ROLE_OWNER = 'owner';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_EDITOR = 'editor';
    public const ROLE_MEMBER = 'member';
    public const ROLE_VIEWER = 'viewer';

    /**
     * Get all available roles.
     *
     * @return array<string>
     */
    public static function availableRoles(): array
    {
        return [
            self::ROLE_OWNER,
            self::ROLE_ADMIN,
            self::ROLE_EDITOR,
            self::ROLE_MEMBER,
            self::ROLE_VIEWER,
        ];
    }

    /**
     * Get the tenant that owns the pivot.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user that owns the pivot.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the role is owner.
     */
    public function isOwner(): bool
    {
        return $this->role === self::ROLE_OWNER;
    }

    /**
     * Check if the role is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if the role can edit content.
     */
    public function canEdit(): bool
    {
        return in_array($this->role, [
            self::ROLE_OWNER,
            self::ROLE_ADMIN,
            self::ROLE_EDITOR,
        ]);
    }

    /**
     * Check if the role has management permissions.
     */
    public function canManage(): bool
    {
        return in_array($this->role, [
            self::ROLE_OWNER,
            self::ROLE_ADMIN,
        ]);
    }
}
