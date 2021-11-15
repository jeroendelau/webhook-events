<?php

namespace StarEditions\WebhookEvent\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookDeliveryLog extends Model
{
    protected $fillable = [
        'webhook_event_id',
        'response_status',
        'response_message',
        'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'timestamp'
    ];

    public function webhookEvent()
    {
        return $this->belongsTo(WebhookDispatch::class, 'webhook_event_id');
    }
}