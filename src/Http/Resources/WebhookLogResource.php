<?php

namespace StarEditions\WebhookEvent\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WebhookLogResource extends JsonResource
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
            'sentAt' => $this->sent_at,
            'status' => $this->response_status,
            'responseCode' => $this->response_message,
        ];
    }
}
