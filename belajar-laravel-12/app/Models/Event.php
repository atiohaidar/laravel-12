<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'location',
        'location_details',
        'capacity',
        'price',
        'is_paid',
        'banner_image',
        'is_published',
        'status'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_paid' => 'boolean',
        'is_published' => 'boolean',
        'price' => 'decimal:2'
    ];

    /**
     * Get the user who created the event.
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the category of the event.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }

    /**
     * Get the registrations for the event.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * Count the number of registered participants.
     */
    public function getRegisteredCountAttribute(): int
    {
        return $this->registrations()
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->count();
    }

    /**
     * Check if the event is free.
     */
    public function getIsFreeAttribute(): bool
    {
        return !$this->is_paid || $this->price <= 0;
    }

    /**
     * Check if the event is full.
     */
    public function getIsFullAttribute(): bool
    {
        return $this->capacity > 0 && $this->registered_count >= $this->capacity;
    }

    /**
     * Check if registration is open.
     */
    public function getIsRegistrationOpenAttribute(): bool
    {
        return $this->is_published && 
               $this->status === 'upcoming' && 
               !$this->is_full &&
               $this->start_time->isFuture();
    }
}
