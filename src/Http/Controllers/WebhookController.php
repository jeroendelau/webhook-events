<?php

namespace StarEditions\WebhookEvent\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use StarEditions\WebhookEvent\Models\Webhook;

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

    public function index()
    {
        # code ...
    }

    public function create(Request $request)
    {
        # code ...
    }

    public function show($id)
    {
        # code ...
    }

    public function update(Request $request, $id)
    {
        # code ...
    }

    public function delete($id)
    {
        # code ...
    }
}