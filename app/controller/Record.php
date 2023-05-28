<?php
declare (strict_types=1);

namespace app\controller;

use app\AdminController;
use app\model\Records;

class Record extends AdminController
{
    protected function initialize()
    {
        parent::initialize();
        $this->model = new Records();
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
        $list = Records::getPageList($where);
        return $this->view('list', compact('list'));
    }

}
