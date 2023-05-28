<?php
declare (strict_types=1);

namespace app\service;

use app\model\Admin;

class AuthServer
{

    /**
     * @var object 对象实例
     */
    protected static $instance;

    protected $logged = false; //登录状态

    protected $_error = ''; //登录状态

    /**
     * 初始化
     * @access public
     * @return AuthServer
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function __get($name)
    {
        return session('admin.' . $name);
    }

    /**
     * 管理员登录
     *
     * @param string $username 用户名
     * @param string $password 密码
     * @return  boolean
     */
    public function login($username, $password)
    {
        $admin = Admin::getByUsername($username);
        if (!$admin) {
            $this->setError('Username is incorrect');
            return false;
        }
        if ($admin['status'] == 'hidden') {
            $this->setError('Admin is forbidden');
            return false;
        }
        if ($admin->loginfailure >= 10 && time() - $admin->updatetime < 86400) {
            $this->setError('Please try again after 1 day');
            return false;
        }
        if ($admin->password != salt_password($password, $admin->salt)) {
            Admin::where('id', intval($admin->id))->inc('loginfailure')->update();
            $this->setError('Password is incorrect');
            return false;
        }
        $admin->loginfailure = 0;
        $admin->logintime = time();
        $admin->loginip = request()->ip();
        $admin->token = uuid();
        $admin->save();
        session('admin', $admin->toArray());
        return true;
    }

    /**
     * 注销登录
     */
    public function logout(): bool
    {
        $admin = Admin::getById(intval($this->id));
        if ($admin) {
            $admin->token = '';
            $admin->save();
        }
        $this->logged = false; //重置登录状态
        session('admin', null);
        return true;
    }

    /**
     * 检测是否登录
     *
     * @return boolean
     */
    public function isLogin(): bool
    {
        if ($this->logged) {
            return true;
        }
//        halt(session('admin'));
        if (!session('?admin')) {
            return false;
        }
        $admin = session('admin');
        //判断是否同一时间同一账号只能在一个地方登录
        $my = Admin::getById(intval($admin['id']));
        if (!$my || $my['token'] != $admin['token']) {
            $this->logout();
            return false;
        }
        //判断管理员IP是否变动
//        if (!isset($admin['loginip']) || $admin['loginip'] != request()->ip()) {
//            $this->logout();
//            return false;
//        }
        $this->logged = true;
        return true;
    }

    /**
     * 设置错误信息
     *
     * @param string $error 错误信息
     * @return AuthServer
     */
    public function setError(string $error): AuthServer
    {
        $this->_error = $error;
        return $this;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError(): string
    {
        return $this->_error ?: '';
    }

}