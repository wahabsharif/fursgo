<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SupportTicketAttachment extends Model
{
    protected $fillable = [
        'support_ticket_id',
        'original_name',
        'path',
        'mime',
        'size',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    public function sizeLabel(): string
    {
        $kilobytes = max(1, (int) round($this->size / 1024));

        return $kilobytes.' KB';
    }

    public function url(): ?string
    {
        return $this->path ? Storage::disk('public')->url($this->path) : null;
    }
}
