<?php

use Webman\Route;

Route::options('/{path:.+}', function () {
    return response('', 204);
});
// 读取模块配置
$modules = config('modules');

foreach ($modules as $name => $module) {
    $prefix      = $module['prefix']     ?? '';
    $routePath   = $module['route_path'] ?? '';
    $middlewares = $module['middleware'] ?? [];
    if (!$routePath || !is_dir($routePath)) {
        continue;
    }
    // 先给模块套一层 /admin 这种前缀
    Route::group($prefix, function () use ($routePath) {
        foreach (glob($routePath . '/*.php') as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            // admin.php 作为“无二级前缀”的通用路由文件
            if ($filename === 'admin') {
                // 里面写 /login、/logout 这种
                require $file;
                continue;
            }
            if ($filename === 'api') {
                // 里面写 /login、/logout 这种
                require $file;
                continue;
            }
            // 其他文件以文件名为前缀：role.php => /role、user.php => /user
            Route::group('/' . $filename, function () use ($file) {
                require $file; // 文件里只写 /list、/info 即可
            });
        }
    })->middleware($middlewares);
}
