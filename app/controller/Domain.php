<?php
declare (strict_types=1);

namespace app\controller;

use app\AdminController;
use app\model\Domains;
use app\model\Records;
use app\model\Sites;
use app\service\CfServer;

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
        if (!empty(input('get.domain'))) {
            $where[] = ['domain', 'like', '%' . trim(input('get.domain')) . '%'];
        }
        if (!empty(input('get.site_id'))) {
            $where[] = ['site_id', '=', intval(input('get.site_id'))];
        }
        $list = Domains::getPageList($where);
        foreach ($list as &$item) {
            $item->site_name = empty($item->sites) ? '' : $item->sites->site_name;
            $item->site_status = empty($item->sites) ? '' : '(' . lang('siteStatus')[$item->sites->status] . ')';
            $item->domainUrl = fix_url($item->domain);
        }
        $siteStatus = lang('siteStatus');
        $sites = Sites::field('id, site_name, status')->select()->column(null, 'id');
        return $this->view('list', compact('list', 'sites', 'siteStatus'));
    }

    public function save()
    {
        if (!$this->permissions()) {
            return message('无权操作');
        }
        $domain = CfServer::instance()->addZone(input('post.domain'));
        if ($domain['success']) {
            request()->withPost(['zone_identifier' => $domain['result']['id']]);
            $domainRes = Domains::create(array_merge(input(), request()->post()));
            if ($domainRes->isEmpty()) {
                return message('Submission Failed');
            }
            if (!empty(input('post.site_id'))) {
                $site = Sites::getById(intval(input('post.site_id')));
                if ($site->deployed == 1) {
                    $records = Records::where('site_id', $site->id)->where('domain_id', $site->a_domain_id)->select()->column('name');
                    if (!empty($records)) {
                        $mainDns = CfServer::instance()->addDns($domain['result']['id'], [
                            'type' => 'CNAME',
                            'name' => '@',
                            'content' => $records[array_rand($records)],
                            'comment' => $site->site_name . '前台',
                        ]);
                        $dns = [];
                        if ($mainDns['success']) {
                            $dns[] = [
                                'domain_id' => $domainRes->id,
                                'site_id' => $site->id,
                                'type' => $mainDns['result']['type'],
                                'name' => $mainDns['result']['name'],
                                'content' => $mainDns['result']['content'],
                                'comment' => $mainDns['result']['comment'],
                                'identifier' => $mainDns['result']['id'],
                            ];
                        }
                        $wwwDns = CfServer::instance()->addDns($domain['result']['id'], [
                            'type' => 'CNAME',
                            'name' => 'www',
                            'content' => $records[array_rand($records)],
                            'comment' => $site->site_name . '前台',
                        ]);
                        if ($wwwDns['success']) {
                            $dns[] = [
                                'domain_id' => $domainRes->id,
                                'site_id' => $site->id,
                                'type' => $wwwDns['result']['type'],
                                'name' => $wwwDns['result']['name'],
                                'content' => $wwwDns['result']['content'],
                                'comment' => $wwwDns['result']['comment'],
                                'identifier' => $wwwDns['result']['id'],
                            ];
                        }
                        empty($dns) ?: (new Records())->saveAll($dns);
                    }
                }
            }
            return message('Submission successfully', false);
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
        $domain = Domains::getById(intval(input('get.id')));
        if (empty($domain->zone_identifier)) {
            return parent::delete();
        }
        // 先删dns记录
        $records = Records::where('domain_id', $domain->id)->select();
        if (!$records->isEmpty()) {
            foreach ($records as $record) {
                if (empty($record->identifier)) {
                    Records::destroy($record->id);
                }
                $delete = CfServer::instance()->delDns($domain->zone_identifier, $record->identifier);
                if ($delete['success'] || ($delete['errors'][0]['code'] == 81044)) {
                    Records::destroy($record->id);
                }
            }
        }

        $delete = CfServer::instance()->delZone($domain->zone_identifier);
        if ($delete['success'] || $delete['errors'][0]['code'] == 1001) {
            return parent::delete();
        }
        return message('Delete failed');
    }

}
