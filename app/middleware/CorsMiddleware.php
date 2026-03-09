<?php
namespace app\middleware;

use Webman\Http\Request;
use Webman\Http\Response;

class CorsMiddleware
{
    public function process(Request $request, callable $handler): Response
    {
        // 允许的前端源（也可以写 *，但带 cookie/token 时不建议）
        $origin = $request->header('origin') ?: '*';

        // 预检请求直接放行
        if ($request->method() === 'OPTIONS') {
            $response = new Response(204);
        } else {
            $response = $handler($request);
        }

        return $response
            ->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Allow-Methods', 'GET,POST,PUT,PATCH,DELETE,OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->withHeader('Access-Control-Max-Age', '86400');
    }
}
