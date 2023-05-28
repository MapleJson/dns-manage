<?php
declare (strict_types=1);

namespace app;

class AdminController extends BaseController
{
    /**
     * @param string $template
     * @param array  $vars
     * @param int    $code
     * @param null   $filter
     * @return \think\response\View
     */
    protected function view(string $template = '', $vars = [], $code = 200, $filter = null): \think\response\View
    {
        $vars['nav'] = config('menu');
        return view($template, $vars, $code, $filter);
    }

}