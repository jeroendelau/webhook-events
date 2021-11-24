<?php

namespace StarEditions\WebhookEvent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use StarEditions\WebhookEvent\Database\Factories\WebhookFactory;

class Webhook extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'owner_type',
        'url',
        'topic',
        'enabled',
        'scope'
    ];

    protected $casts = [
        'enabled' => 'boolean'
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

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return WebhookFactory::new();
    }
}
