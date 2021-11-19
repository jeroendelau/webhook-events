<?php

namespace StarEditions\WebhookEvent\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use StarEditions\WebhookEvent\Http\Resources\WebhookDispatchResource;
use StarEditions\WebhookEvent\Http\Resources\WebhookLogResource;
use StarEditions\WebhookEvent\Models\WebhookDispatch;

class WebhookEventController
{
    public function __construct()
    {
        if(!Auth::check()) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }

    public function index(Request $request)
    {
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
        return new WebhookDispatchResource($webhookDispatch);
    }

    public function log(WebhookDispatch $webhookDispatch)
    {
        $log = $webhookDispatch->log()
        ->latest('sent_at')
        ->get();
        return WebhookLogResource::collection($log);
    }
}