<?php
declare (strict_types=1);

namespace app\controller;

use app\AdminController;
use app\model\Admin;

class User extends AdminController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $admin = Admin::find(session('admin.id'));
        return $this->view('profile', [
            'username'   => $admin->username,
            'nickname'   => $admin->nickname,
            'createTime' => $admin->create_time,
        ]);
    }

    /**
     * 保存更新的资源
     *
     * @return \think\Response
     */
    public function update()
    {
        $result = Admin::update(
            ['password' => salt_password(input('put.password'), session('admin.salt'))],
            ['id' => session('admin.id')]
        );
        if ($result) {
            return message('修改密码成功', false);
        }
        return message('修改密码失败');
    }

}
