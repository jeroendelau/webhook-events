<?php

namespace StarEditions\WebhookEvent\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WebhookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'topic' => ['required', Rule::in(config('webhook-events-server.topics'))],
            'url' => ['required', 'url'],
            'sometimes' => ['required', 'boolean']
        ];
    }
}
