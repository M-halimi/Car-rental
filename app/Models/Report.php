<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    protected $fillable = [
        'reportable_type',
        'reportable_id',
        'reported_by',
        'reason',
        'description',
        'status',
        'moderated_by',
        'moderated_at',
        'moderation_notes',
    ];

    protected function casts(): array
    {
        return [
            'moderated_at' => 'datetime',
        ];
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }
}
