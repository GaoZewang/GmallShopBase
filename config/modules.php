<?php
// config/modules.php

return [
    'admin' => [
        'prefix'     => '/admin',                      // 路由前缀
        'route_path' => base_path() . '/app/admin/route',
        'middleware' => [
            app\middleware\CorsMiddleware::class
        ],
    ],
    'api' => [
        'prefix'     => '/api',
        'route_path' => base_path() . '/app/api/route',
        'middleware' => [],
    ],

    // 后面要加 merchant/store 也在这里写
    // 'merchant' => [...],
    // 'store'    => [...],
];
