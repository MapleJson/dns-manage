<?php
declare (strict_types=1);

namespace app\controller;

use app\AdminController;
use app\model\Admin as Ad;

class Domain extends AdminController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     * @throws \think\db\exception\DbException
     */
    public function index()
    {
        $where = [];
        if (!empty(input('get.status'))) {
            $where[] = ['status', '=', intval(input('get.status'))];
        }
        if (!empty(input('get.username'))) {
            $where[] = ['username', 'like', '%' . trim(input('get.username')) . '%'];
        }
        $list = Ad::getPageList($where);
        $status = lang('userStatus');
        return $this->view('list', compact('list', 'status'));
    }

    /**
     * 保存新建的资源
     *
     * @return \think\Response
     */
    public function save()
    {
        $salt = salt();
        $result = Ad::create([
            'username' => input('post.username'),
            'nickname' => empty(input('post.nickname')) ? input('post.username') : input('post.nickname'),
            'password' => salt_password(input('post.password'), $salt),
            'salt'     => $salt,
        ]);
        if ($result->id) {
            return message('Submitted successfully', false);
        }
        return message('Submission Failed');
    }

    /**
     * 保存更新的资源
     *
     * @param Ad $model
     * @return \think\Response
     */
    public function update(Ad $model)
    {
        $admin = $model->getById(intval(input('get.id')));
        if (!$admin->isExists()) {
            return message('The agent does not exist');
        }

        $result = $model->update(['status' => intval(input('get.status'))], ['id' => intval(input('get.id'))]);
        if ($result) {
            return message('Admin modified successfully', false);
        }
        return message('Admin modification failed');
    }

    /**
     * @param Ad $model
     * @return mixed
     */
    public function reset(Ad $model)
    {
        $agent = $model->getById(intval(input('get.id')));
        if (!$agent->isExists()) {
            return message('The admin does not exist');
        }
        $salt = salt();
        $result = $model->update([
            'password' => salt_password('123456789', $salt),
            'salt' => $salt
        ], ['id' => intval(input('get.id'))]);
        if ($result) {
            return message('Admin modified successfully', false);
        }
        return message('Admin modification failed');
    }
}
