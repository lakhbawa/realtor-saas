<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactSubmission extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'property_id',
        'name',
        'email',
        'phone',
        'message',
        'is_read',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
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
    }

    /**
     * Get the user that owns the contact submission.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the property related to the submission (if any).
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Scope a query to only include unread submissions.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to order by newest first.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Mark submission as read.
     */
    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Mark submission as unread.
     */
    public function markAsUnread(): void
    {
        $this->update(['is_read' => false]);
    }

    /**
     * Get message preview (truncated).
     */
    public function getMessagePreviewAttribute(): string
    {
        return \Illuminate\Support\Str::limit($this->message, 50);
    }
}
