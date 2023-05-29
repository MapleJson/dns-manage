<?php
declare (strict_types=1);

namespace app\controller;

use app\AdminController;
use app\model\Domains;
use app\model\Records;
use app\model\Servers;
use app\model\Sites;

class Site extends AdminController
{
    protected function initialize()
    {
        parent::initialize();
        $this->model = new Sites();
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
        if (!empty(input('get.site_name'))) {
            $where[] = ['site_name', 'like', '%' . trim(input('get.site_name')) . '%'];
        }
        if (!empty(input('get.web_domains'))) {
            $where[] = ['web_domains', 'like', '%' . trim(input('get.web_domains')) . '%'];
        }
        if (!empty(input('get.server_id'))) {
            $where[] = ['server_id', '=', intval(input('get.server_id'))];
        }
        if (!empty(input('get.front_server_id'))) {
            $where[] = ['front_server_id', '=', intval(input('get.front_server_id'))];
        }
        if (!empty(input('get.a_domain_id'))) {
            $where[] = ['a_domain_id', '=', intval(input('get.a_domain_id'))];
        }
        $list = Sites::getPageList($where);
        $siteStatus = lang('siteStatus');
        foreach ($list as &$item) {
            $item->server = empty($item->servers) ? '' : $item->servers->server_name;
            $item->frontServer = empty($item->frontServers) ? '' : $item->frontServers->server_name;
            $item->aDomain = empty($item->domains) ? '' : $item->domains->domain;
            $item->aDns = empty($item->records) ? '' : $item->records->name;
        }
        $domains = Domains::field('id, domain')->select()->column(null, 'id');
        $servers = Servers::field('id, server_name')->where('type', 1)->select()->column(null, 'id');
        $frontServers = Servers::field('id, server_name')->where('type', 2)->select()->column(null, 'id');
        return $this->view('list', compact('list', 'siteStatus', 'domains', 'servers', 'frontServers'));
    }

    public function deploy()
    {
        return message('success');
    }

}
