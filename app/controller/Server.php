<?php
declare (strict_types=1);

namespace app\controller;

use app\AdminController;
use app\model\Servers;

class Server extends AdminController
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
        if (!empty(input('get.ip'))) {
            $where[] = ['ip', 'like', '%' . trim(input('get.ip')) . '%'];
        }
        $list = Servers::getPageList($where);
        return $this->view('list', compact('list'));
    }

    /**
     * 保存新建的资源
     *
     * @return \think\Response
     */
    public function save()
    {
        $result = Servers::create([
            'server_name' => input('post.server_name'),
            'public_ip' => input('post.public_ip'),
            'private_ip' => input('post.private_ip'),
            'remark'     => input('post.remark'),
        ]);
        if ($result->id) {
            return message('Submitted successfully', false);
        }
        return message('Submission Failed');
    }

    /**
     * 保存更新的资源
     *
     * @param Servers $model
     * @return \think\Response
     */
    public function update(Servers $model)
    {
        $data = $model->getById(intval(input('post.id')));
        if (!$data->isExists()) {
            return message('The agent does not exist');
        }

        $result = $model->update([
            'status' => intval(input('post.status'))
        ], ['id' => intval(input('post.id'))]);
        if ($result) {
            return message('Edit successfully', false);
        }
        return message('Edit failed');
    }

    /**
     * @param Servers $model
     * @return mixed
     */
    public function delete(Servers $model)
    {
        if ($model->where('id', intval(input('get.id')))->delete()) {
            return message('Delete successfully', false);
        }
        return message('Delete failed');
    }
}
