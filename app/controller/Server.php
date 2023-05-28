<?php
declare (strict_types=1);

namespace app\controller;

use app\AdminController;
use app\model\Servers;

class Server extends AdminController
{
    protected function initialize()
    {
        parent::initialize();
        $this->model = new Servers();
    }

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
        if (!empty(input('get.type'))) {
            $where[] = ['type', '=', intval(input('get.type'))];
        }
        $list = Servers::getPageList($where);
        $type = lang('type');
        return $this->view('list', compact('list', 'type'));
    }

}
