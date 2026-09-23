<?php

return [
    'secret_key' => env('XENDIT_SECRET_KEY'),
    'callback_token' => env('XENDIT_CALLBACK_TOKEN'),
    'is_production' => env('XENDIT_IS_PRODUCTION', false),
    // Client ID Xendit, wajib untuk charge OVO (channel ID_OVO).
    'ovo_app_id' => env('XENDIT_OVO_APP_ID'),
];
