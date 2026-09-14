<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    protected $fillable = [
        'ticket_number',
        'user_id',
        'goormer_spacer_id',
        'email',
        'subject',
        'category',
        'booking_reference',
        'description',
        'status',
        'audience',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function groomerSpacer(): BelongsTo
    {
        return $this->belongsTo(GroomerSpacerProfile::class, 'goormer_spacer_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(SupportTicketAttachment::class);
    }

    public static function nextTicketNumber(): string
    {
        $nextId = (int) (static::query()->max('id') ?? 0) + 1;

        return 'FG-'.str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
    }
}
