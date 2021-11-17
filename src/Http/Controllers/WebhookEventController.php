<?php

namespace StarEditions\WebhookEvent\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function index()
    {
        # code ...
    }

    public function show($id)
    {
        # code ...
    }

    public function log($id)
    {
        # code ...
    }
}