<?php

namespace Modules\Forms\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'user_id',
        'respondent_email',
        'respondent_ip',
    ];

    /**
     * Get the form that this response belongs to.
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Get the user that submitted this response.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all answers for this response.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(FormAnswer::class);
    }
}
