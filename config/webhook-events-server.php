<?php

return [
    /*
     *  A list of topics that can be registered
     */
    'topics' => [],

    /**
     * API routes path
     */
    'path' => '/api',

    /**
     * Middlware to protect api routes
     */
    'middleware' => 'auth:api'
];
