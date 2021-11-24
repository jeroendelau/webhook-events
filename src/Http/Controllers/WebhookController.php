<?php

namespace StarEditions\WebhookEvent\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
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
        $webhookOwner = Auth::user();
        if(Auth::user() instanceof ProvidesWebhookOwner) {
            if(!in_array(HasWebhooks::class, class_uses(Auth::user()->getWebhookOwner()))) {
                abort(401, 'Access denied');
            }
            $webhookOwner = Auth::user()->getWebhookOwner();
        }elseif (!in_array(HasWebhooks::class, class_uses(Auth::user()))) {
            abort(401, 'Access denied');
        }
        $query = Webhook::query();
        $query->webhookOwner($webhookOwner);
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
        $webhookOwner = Auth::user();
        if(Auth::user() instanceof ProvidesWebhookOwner) {
            if(!in_array(HasWebhooks::class, class_uses(Auth::user()->getWebhookOwner()))) {
                abort(401, 'Access denied');
            }
            $webhookOwner = Auth::user()->getWebhookOwner();
        }elseif (!in_array(HasWebhooks::class, class_uses(Auth::user()))) {
            abort(401, 'Access denied');
        }
        if(!$this->checkUrl($request->url)) {
            abort(422, 'URL does not exist.');
        }

        $data = $request->validated();
        $data['enabled'] = $request->has('enabled') ? $request->enabled : true;
        if(!($webhookOwner instanceof MightOverWriteScope)) {
            $data['scope'] = $webhookOwner->getWebhookScope();
        }elseif($webhookOwner instanceof MightOverWriteScope && !$request->has('scope')) {
            $data['scope'] = $webhookOwner->getWebhookScope();
        }elseif($webhookOwner instanceof MightOverWriteScope && !$webhookOwner->canOverwriteScope()) {
            $data['scope'] = $webhookOwner->getWebhookScope();
        }

        $data['owner_id'] = $webhookOwner->id;
        $data['owner_type'] = get_class($webhookOwner);
        
        $webhook = Webhook::create($data);
        return response(new WebhookResource($webhook));
    }

    public function show($id)
    {
        $webhookOwner = Auth::user();
        if(Auth::user() instanceof ProvidesWebhookOwner) {
            $webhookOwner = Auth::user()->getWebhookOwner();
        }
        $webhook = Webhook::where('id', $id)
        ->where('owner_id', $webhookOwner->id)
        ->where('owner_type', get_class($webhookOwner))
        ->first();
        if(!$webhook) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
        return new WebhookResource($webhook);
    }

    public function update(WebhookRequest $request, $id)
    {
        $webhookOwner = Auth::user();
        if(Auth::user() instanceof ProvidesWebhookOwner) {
            if(!in_array(HasWebhooks::class, class_uses(Auth::user()->getWebhookOwner()))) {
                abort(401, 'Access denied');
            }
            $webhookOwner = Auth::user()->getWebhookOwner();
        }elseif (!in_array(HasWebhooks::class, class_uses(Auth::user()))) {
            abort(401, 'Access denied');
        }

        if(!$this->checkUrl($request->url)) {
            abort(422, 'URL does not exist.');
        }

        $webhook = Webhook::findOrFail($id);
        $data = $request->validated();
        $data['enabled'] = $request->has('enabled') ? $request->enabled : true;
        if(!($webhookOwner instanceof MightOverWriteScope)) {
            $data['scope'] = $webhookOwner->getWebhookScope();
        }elseif($webhookOwner instanceof MightOverWriteScope && !$request->has('scope')) {
            $data['scope'] = $webhookOwner->getWebhookScope();
        }elseif($webhookOwner instanceof MightOverWriteScope && !$webhookOwner->canOverwriteScope()) {
            $data['scope'] = $webhookOwner->getWebhookScope();
        }
        $webhook->update($data);
        return response()->json([
            'status' => 'ok'
        ]);
    }

    public function destroy($id)
    {
        $webhookOwner = Auth::user();
        if(Auth::user() instanceof ProvidesWebhookOwner) {
            $webhookOwner = Auth::user()->getWebhookOwner();
        }
        $webhook = Webhook::where('id', $id)
        ->where('owner_id', $webhookOwner->id)
        ->where('owner_type', get_class($webhookOwner))
        ->first();
        if(!$webhook) {
            return response()->json(['message' => 'Resource not found'], 404);
        }

        $webhook->delete();
        return response()->json([
            'status' => 'ok'
        ]);
    }

    private function checkUrl($url)
    {
        try {
            $response = Http::get($url);
            return $response->successful();
        } catch (\Throwable $th) {
            return false;
        }
    }
}
