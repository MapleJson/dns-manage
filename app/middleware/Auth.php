<?php
declare (strict_types = 1);

namespace app\middleware;

use app\service\AuthServer;

class Auth
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return mixed|\think\response\Redirect
     */
    public function handle($request, \Closure $next)
    {
        if (!AuthServer::instance()->isLogin()) {
            return redirect((string)url('login'));
        }
        return $next($request);
    }
}
