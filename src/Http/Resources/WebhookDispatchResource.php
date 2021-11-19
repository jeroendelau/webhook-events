<?php

namespace StarEditions\WebhookEvent\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WebhookDispatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $lastAttempt = $this->log()->latest()->first();
        return [
            'id' => $this->id,
            'webhook_id' => $this->webhook_id,
            'topic' => $this->topic,
            'payload' => $this->payload,
            'status' => $lastAttempt?->response_status,
            'createdAt' => $this->created_at,
            'lastSentAt' => $this->last_attempt,
            'lastResponseMessage' => $lastAttempt?->response_message,
            'deliveryLog' => WebhookLogResource::collection($this->log),
            'webhook' => $this->webhook
        ];
    }
}
