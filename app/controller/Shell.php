<?php
declare (strict_types=1);

namespace app\controller;

use app\AdminController;
use app\model\Shell as Exec;
use app\model\Sites;

class Shell extends AdminController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     * @throws \think\db\exception\DbException
     */
    public function index()
    {
        $where = [
            'status' => 1
        ];
        if (!empty(input('get.site_id'))) {
            $where['site_id'] = input('get.site_id');
        }
        $list = Exec::where($where)->order('id', 'asc')->paginate(20);
        $sites = Sites::field('id, site_name')->select()->column(null, 'id');
        return $this->view('list', compact('list', 'sites'));
    }

    public function do()
    {
        if (!$this->permissions()) {
            return message('无权操作');
        }
        $shell = Exec::getById(intval(input('get.id')));
        if (empty($shell)) {
            return message('The data does not exist');
        }
        $shell->status = 2;
        $shell->save();
        $exec = shell_exec($shell->shell);
        return message($exec ?: 'Exec successfully', false);
    }

}
