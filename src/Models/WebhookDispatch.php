<?php

namespace StarEditions\WebhookEvent\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookDispatch extends Model
{
    protected $fillable = [
        'webhook_id',
        'topic',
        'payload',
        'last_attempt',
        'success',
        'attempts'
    ];

    public function webhook()
    {
        return $this->belongsTo(Webhook::class);
    }

    public function log()
    {
        return $this->hasMany(WebhookDeliveryLog::class, 'webhook_event_id');
    }
}