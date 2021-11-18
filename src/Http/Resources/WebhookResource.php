<?php

namespace StarEditions\WebhookEvent\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WebhookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'topic' => $this->topic,
            'scope' => $this->scope,
            'createdAt' => $this->created_at
        ];
    }
}
