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

if (!function_exists('curl_http')) {
    /**
     * CURL GET || post请求
     * @desc: GET与post都通用
     * @param: $url 请求的地址
     *       $isPostRequest 默认true是GET请求，否则是POST请求
     *       $data array  请求的参数
     *       $certParam  array  ['cert_path']    ['key_path']
     * @return:
     */
    function curl_http($url, $isPostRequest = false, $data = [], $header = [], $certParam = [])
    { // 模拟提交数据函数
        $curlObj = curl_init(); // 启动一个CURL会话
        //如果是POST请求
        if ($isPostRequest) {
            curl_setopt($curlObj, CURLOPT_POST, 1); // 发送一个常规的Post请求
            curl_setopt($curlObj, CURLOPT_POSTFIELDS, json_encode($data)); // Post提交的数据包
        } else {  //get请求检查是否拼接了参数，如果没有，检查$data是否有参数，有参数就进行拼接操作
            $getParamStr = '';
            if (!empty($data) && is_array($data)) {
                $tmpArr = [];
                foreach ($data as $k => $v) {
                    $tmpArr[] = $k . '=' . $v;
                }
                $getParamStr = implode('&', $tmpArr);
            }
            //检查链接中是否有参数
            $url .= strpos($url, '?') !== false ? '&' . $getParamStr : '?' . $getParamStr;
        }
        curl_setopt($curlObj, CURLOPT_URL, $url); // 要访问的地址
        //检查链接是否https请求
        if (strpos($url, 'https') !== false) {
            //设置证书
            if (!empty($certParam) && isset($certParam['cert_path']) && isset($certParam['key_path'])) {
                curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
                curl_setopt($curlObj, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
                //设置证书
                //使用证书：cert 与 key 分别属于两个.pem文件
                curl_setopt($curlObj, CURLOPT_SSLCERTTYPE, 'PEM');
                curl_setopt($curlObj, CURLOPT_SSLCERT, $certParam['cert_path']);
                curl_setopt($curlObj, CURLOPT_SSLKEYTYPE, 'PEM');
                curl_setopt($curlObj, CURLOPT_SSLKEY, $certParam['key_path']);
            } else {
                curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
                curl_setopt($curlObj, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
            }
        }
        // 模拟用户使用的浏览器
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            curl_setopt($curlObj, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        }
        curl_setopt($curlObj, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curlObj, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curlObj, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curlObj, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, $header);   //设置头部
        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $result = curl_exec($curlObj); // 执行操作
        if (curl_errno($curlObj)) {
            $result = 'error: ' . curl_error($curlObj);//捕抓异常
        }
        curl_close($curlObj); // 关闭CURL会话
        return $result; // 返回数据，json格式
    }
}
