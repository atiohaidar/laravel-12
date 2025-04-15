<?php

namespace Modules\Forms\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'question_text',
        'question_type',
        'is_required',
        'options',
        'order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'options' => 'array',
    ];

    /**
     * Get the form that owns the question.
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Get the answers for this question.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(FormAnswer::class);
    }

    /**
     * Get available question types.
     */
    public static function getQuestionTypes(): array
    {
        return [
            'text' => 'Short Text',
            'textarea' => 'Long Text',
            'number' => 'Number',
            'email' => 'Email',
            'radio' => 'Single Choice',
            'checkbox' => 'Multiple Choice',
            'select' => 'Dropdown',
            'date' => 'Date',
        ];
    }
}
