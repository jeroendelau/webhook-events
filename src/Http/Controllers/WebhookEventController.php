<?php

namespace StarEditions\WebhookEvent\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use StarEditions\WebhookEvent\Http\Resources\WebhookDispatchResource;
use StarEditions\WebhookEvent\Http\Resources\WebhookLogResource;
use StarEditions\WebhookEvent\Models\WebhookDispatch;
use StarEditions\WebhookEvent\HasWebhooks;

class WebhookEventController extends Controller
{

    public function index(Request $request)
    {
        if (!in_array(HasWebhooks::class, class_uses(Auth::user()))) {
            abort(401, 'Access denied');
        }

        $query = WebhookDispatch::query();
        $query->whereHas('webhook', function($webhookQuery) {
            $webhookQuery->where('owner_id', Auth::id())
            ->where('owner_type', get_class(Auth::user()->getWebhookOwner()));
        });
        $query->when($request->sort, function($sortQuery, $sort) use($request) {
            $sortQuery->orderBy($sort, $request->order ?? 'ASC');
        });
        $events = $query->paginate();
        return WebhookDispatchResource::collection($events);
    }

    public function show(WebhookDispatch $webhookDispatch)
    {
        if (!in_array(HasWebhooks::class, class_uses(Auth::user()))) {
            abort(401, 'Access denied');
        }

        return new WebhookDispatchResource($webhookDispatch);
    }

    public function log(WebhookDispatch $webhookDispatch)
    {
        if (!in_array(HasWebhooks::class, class_uses(Auth::user()))) {
            abort(401, 'Access denied');
        }
        
        $log = $webhookDispatch->log()
        ->latest('sent_at')
        ->get();
        return WebhookLogResource::collection($log);
    }
}