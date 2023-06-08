<?php
declare (strict_types=1);

namespace app\controller;

use app\AdminController;
use app\model\Domains;
use app\model\Records;
use app\model\Sites;
use app\service\CfServer;

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
            $item->nameUrl = fix_url($item->name);
        }
        $siteStatus = lang('siteStatus');
        $domains = Domains::field('id, domain')->select()->column(null, 'id');
        $sites = Sites::field('id, site_name')->select()->column(null, 'id');
        return $this->view('list', compact('list', 'domains', 'sites', 'siteStatus'));
    }

    public function save()
    {
        if (!$this->permissions()) {
            return message('无权操作');
        }
        $site = Sites::getById(intval(input('post.site_id')));
        $domain = Domains::getById(intval(input('post.domain_id')));
        $dns = [
            'type' => input('post.type'),
            'name' => input('post.name'),
            'content' => input('post.content'),
            'comment' => input('post.comment'),
        ];
        if (empty($dns['comment'])) {
            $dns['comment'] = $site->site_name;
        }
        $record = CfServer::instance()->addDns($domain->zone_identifier, $dns);
        if ($record['success']) {
            request()->withPost([
                'name' => $record['result']['name'],
                'identifier' => $record['result']['id']
            ]);
            return parent::save();
        }
        return message('Submission Failed');
    }

    /**
     * 删除选中的资源
     * @return mixed
     */
    public function delete()
    {
        if (!$this->permissions()) {
            return message('无权操作');
        }
        $record = Records::getById(intval(input('get.id')));
        if (empty($record->domains->zone_identifier) || empty($record->identifier)) {
            return parent::delete();
        }
        $delete = CfServer::instance()->delDns($record->domains->zone_identifier, $record->identifier);

        if ($delete['success']) {
            return parent::delete();
        }
        if ($delete['errors'][0]['code'] == 81044) {
            return parent::delete();
        }
        return message('Delete failed');
    }

}
