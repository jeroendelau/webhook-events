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

    public function scopeWebhookOwner($query, $owner)
    {
        $query->where('owner_id', $owner->id)
        ->where('owner_type', get_class($owner));
    }

    public function scopeTopic($query, $topic)
    {
        if($topic) {
            $query->where('topic', 'like', "%$topic%");
        }
    }

    public function scopeUrl($query, $url)
    {
        if($url) {
            $query->where('url', 'like', "%$url%");
        }
    }

    public function scopeCreatedAt($query, $created_at)
    {
        if($created_at) {
            $query->where('created_at', 'like', "$created_at");
        }
    }

    public function scopeEnabled($query, $enabled)
    {
        if($enabled !== null) {
            $query->where('enabled', $enabled);
        }
    }
}