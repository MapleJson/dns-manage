<?php
declare (strict_types=1);

namespace app\controller;

use app\AdminController;
use app\model\Domains;
use app\model\Records;
use app\model\Sites;

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
        if (!empty(input('get.domain_id'))) {
            $where['domain_id'] = input('get.domain_id');
        }
        if (!empty(input('get.site_id'))) {
            $where['site_id'] = input('get.site_id');
        }
        $list = Records::getPageList($where);
        foreach ($list as &$item) {
            $item->domain = empty($item->domains) ? '' : $item->domains->domain;
            $item->site_name = empty($item->sites) ? '' : $item->sites->site_name;
        }
        $domains = Domains::field('id, domain')->select()->column(null, 'id');
        $sites = Sites::field('id, site_name')->select()->column(null, 'id');
        return $this->view('list', compact('list', 'domains', 'sites'));
    }

    public function save()
    {
        if (session('admin.username') !== 'admin') {
            return message('无权操作');
        }

        return parent::save();
    }

}
