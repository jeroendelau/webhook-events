<?php

namespace StarEditions\WebhookEvent\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use StarEditions\WebhookEvent\Models\Webhook;
use StarEditions\WebhookEvent\Http\Resources\WebhookResource;
use StarEditions\WebhookEvent\Http\Requests\WebhookRequest;
use StarEditions\WebhookEvent\HasWebhooks;
use StarEditions\WebhookEvent\MightOverWriteScope;
use StarEditions\WebhookEvent\ProvidesWebhookOwner;

class WebhookController extends Controller
{
    public function index(Request $request)
    {
        if (!in_array(HasWebhooks::class, class_uses(Auth::user()))) {
            abort(401, 'Access denied');
        }

        $query = Webhook::query();
        if(Auth::user() instanceof ProvidesWebhookOwner) {
            $query->webhookOwner(Auth::user()->getWebhookOwner());
        }else {
            $query->webhookOwner(Auth::user());
        }
        $webhooks = $query->url($request->url)
        ->topic($request->topic)
        ->createdAt($request->created_at)
        ->enabled($request->enabled)
        ->when($request->sort, function($sortQuery, $sort) use($request) {
            $sortQuery->orderBy($sort, $request->order ?? 'ASC');
        })
        ->paginate();
        return WebhookResource::collection($webhooks);
    }

    public function store(WebhookRequest $request)
    {
        if (!in_array(HasWebhooks::class, class_uses(Auth::user()))) {
            abort(401, 'Access denied');
        }
        $data = $request->validated();
        if(!(Auth::user() instanceof MightOverWriteScope)) {
            $data['scope'] = Auth::user()->getWebhookScope();
        }elseif(Auth::user() instanceof MightOverWriteScope && !$request->has('scope')) {
            $data['scope'] = Auth::user()->getWebhookScope();
        }
        if((Auth::user() instanceof ProvidesWebhookOwner)) {
            
            $data['owner_id'] = Auth::user()->getWebhookOwner()->id;
            $data['owner_type'] = get_class(Auth::user()->getWebhookOwner());
        }else {
            $data['owner_id'] = Auth::id();
            $data['owner_type'] = get_class(Auth::user());
        }
        
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
        if(!(Auth::user() instanceof MightOverWriteScope)) {
            $data['scope'] = Auth::user()->getWebhookScope();
        }elseif(Auth::user() instanceof MightOverWriteScope && !$request->has('scope')) {
            $data['scope'] = Auth::user()->getWebhookScope();
        }
        $webhook->update($data);
        return response()->json([
            'status' => 'ok'
        ]);
    }

    public function destroy(Webhook $webhook)
    {
        $webhook->delete();
        return response()->json([
            'status' => 'ok'
        ]);
    }
}
