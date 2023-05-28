<?php
declare (strict_types=1);

namespace app\controller;

use app\AdminController;
use app\model\Domains;

class Domain extends AdminController
{
    protected function initialize()
    {
        parent::initialize();
        $this->model = new Domains();
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
        $list = Domains::getPageList($where);
        return $this->view('list', compact('list'));
    }

}
