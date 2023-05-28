<?php

namespace app\controller;

use app\service\AuthServer;
use app\AdminController;

class Login extends AdminController
{
    // 初始化
    protected function initialize()
    {
        parent::initialize();
        //移除HTML标签
        $this->request->filter('trim,strip_tags,htmlspecialchars');
        $this->auth = AuthServer::instance();
    }

    public function login()
    {
        return $this->view('login');
    }

    /**
     * 管理员登录
     *
     * @return \think\response\Redirect|\think\response\View
     */
    public function loginDo()
    {
        if ($this->auth->isLogin()) {
            return redirect((string)url('home'));
        }

        $result = $this->auth->login(input('post.username', '', 'string'), input('post.password', '', 'string'));
        if ($result === true) {
            return redirect((string)url('home'));
        }
        return message($this->auth->getError() ?: 'Username or password is incorrect');
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        $this->auth->logout();
        return redirect((string)url('login'));
    }

}