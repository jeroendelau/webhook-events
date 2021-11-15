<?php

use Illuminate\Support\Facades\Route;

Route::resource('webhook', 'WebhookController');
Route::get('webhook/event', 'WebhookEventController@index');
Route::get('webhook/event/{id}', 'WebhookEventController@show');
Route::get('webhook/event/{id}/log', 'WebhookEventController@logs');