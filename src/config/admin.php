<?php

declare(strict_types=1);

return [
    /**
     * 初期管理者
     */
    'init' => [
        'name' => env('INIT_ADMIN_NAME'),
        'email' => env('INIT_ADMIN_EMAIL'),
        'password' => env('INIT_ADMIN_PASSWORD'),
    ],
];
