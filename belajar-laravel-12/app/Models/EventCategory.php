<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description'
    ];

    /**
     * Get the events for the category.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'category_id');
    }

    /**
     * Get the count of published events in this category.
     */
    public function getEventsCountAttribute(): int
    {
        return $this->events()->where('is_published', true)->count();
    }
}
