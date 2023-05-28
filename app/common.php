<?php
// 应用公共文件

if (!function_exists('uuid')) {
    /**
     * 获取全球唯一标识
     *
     * @return string
     */
    function uuid(): string
    {
        return sprintf(
            '%04x%04x%04x%04x%04x%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}

if (!function_exists('salt')) {
    /**
     * 获取全球唯一标识
     *
     * @return string
     */
    function salt(): string
    {
        $str = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm!@#$%^&*()_+|,.";
        $length = mt_rand(8, 15);
        return substr(str_shuffle($str), mt_rand(0, strlen($str) - $length + 1), $length);
    }
}

if (!function_exists('message')) {
    /**
     * @param string $message
     * @param bool   $failed
     * @return \think\response\View
     */
    function message(string $message = 'Illegal request', bool $failed = true): \think\response\View
    {
        $wait = 3;
        if ($failed) {
            $wait = 5;
        }
        $message = lang($message);
        $url = request()->header('referer');
        return view('common/message', compact('wait', 'message', 'url'));
    }
}

if (!function_exists('salt_password')) {
    /**
     * 生成加盐密码
     *
     * @param string $password
     * @param string $salt
     * @return string
     */
    function salt_password(string $password, string $salt = ''): string
    {
        return md5(md5($password) . $salt);
    }
}
