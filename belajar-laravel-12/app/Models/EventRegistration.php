<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'event_id',
        'user_id',
        'registration_number',
        'amount_paid',
        'transaction_id',
        'status',
        'checked_in_at',
        'notes'
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'checked_in_at' => 'datetime'
    ];

    /**
     * Generate a unique registration number.
     */
    public static function generateRegistrationNumber(): string
    {
        return 'REG-' . strtoupper(uniqid());
    }

    /**
     * Get the event of this registration.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user who registered.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transaction for the registration.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Check if the registration is active.
     */
    public function getIsActiveAttribute(): bool
    {
        return !in_array($this->status, ['cancelled', 'refunded']);
    }

    /**
     * Check if the registration has been checked in.
     */
    public function getIsCheckedInAttribute(): bool
    {
        return $this->checked_in_at !== null;
    }
}
