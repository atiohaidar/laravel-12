<?php

namespace Modules\Forms\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_response_id',
        'form_question_id',
        'answer_value',
        'answer_values',
    ];

    protected $casts = [
        'answer_values' => 'array',
    ];

    /**
     * Get the response that this answer belongs to.
     */
    public function response(): BelongsTo
    {
        return $this->belongsTo(FormResponse::class, 'form_response_id');
    }

    /**
     * Get the question that this answer belongs to.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(FormQuestion::class, 'form_question_id');
    }
}
