<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    const CHANNEL_SMS = 'SMS';
    const CHANNEL_EMAIL = 'EMAIL';

    const STATUS_CREATED = 'CREATED';
    const STATUS_SENT = 'SENT';

    protected $fillable = [
        'client_id',
        'channel',
        'content',
        'status'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}