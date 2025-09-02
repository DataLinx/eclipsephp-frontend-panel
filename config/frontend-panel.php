<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enable/disable guest access
    |--------------------------------------------------------------------------
    |
    | This option controls whether guests can access the frontend panel
    | without authentication. When enabled, users can view the panel
    | without needing to log in.
    |
    */
    'guest_access' => (bool) env('FRONTEND_GUEST_ACCESS', true),
];
