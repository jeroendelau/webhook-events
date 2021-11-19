<?php

namespace StarEditions\WebhookEvent\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use StarEditions\WebhookEvent\Models\Webhook;
use StarEditions\WebhookEvent\Http\Resources\WebhookResource;
use StarEditions\WebhookEvent\Requests\WebhookRequest;

class WebhookController
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
        $webhooks = Webhook::webhookOwner(Auth::user()->getWebhookOwner())
        ->url($request->url)
        ->topic($request->topic)
        ->createdAt($request->created_at)
        ->enabled($request->enabled)
        ->when($request->sort, function($sortQuery, $sort) use($request) {
            $sortQuery->orderBy($sort, $request->order ?? 'ASC');
        })
        ->paginate();
        return WebhookResource::collection($webhooks);
    }

    public function create(WebhookRequest $request)
    {
        $data = $request->validated();
        $data['scope'] = Auth::user()->getWebhookScope();
        $data['owner_id'] = Auth::user()->getWebhookOwner()->id;
        $data['owner_type'] = get_class(Auth::user()->getWebhookOwner());
        Webhook::create($data);
        return response()->json([
            'status' => 'ok'
        ]);
    }

    public function show(Webhook $webhook)
    {
        return new WebhookResource($webhook);
    }

    public function update(WebhookRequest $request, Webhook $webhook)
    {
        $data = $request->validated();
        $data['scope'] = Auth::user()->getWebhookScope();
        $webhook->update($data);
        return response()->json([
            'status' => 'ok'
        ]);
    }

    public function delete(Webhook $webhook)
    {
        $webhook->delete();
        return response()->json([
            'status' => 'ok'
        ]);
    }
}