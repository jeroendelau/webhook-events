<?php

namespace StarEditions\WebhookEvent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Webhook extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'owner_id',
        'owner_type',
        'url',
        'topic',
        'enabled',
        'scope'
    ];

    public function dispatches()
    {
        return $this->hasMany(WebhookDispatch::class);
    }

    public function owner()
    {
        return $this->morphTo(__FUNCTION__, 'owner_type', 'owner_id');
    }
}