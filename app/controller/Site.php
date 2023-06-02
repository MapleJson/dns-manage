<?php
declare (strict_types=1);

namespace app\controller;

use app\AdminController;
use app\model\Domains;
use app\model\Records;
use app\model\Sites;
use app\model\Deploy;
use app\service\CfServer;

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
        if (!empty(input('get.a_domain_id'))) {
            $where[] = ['a_domain_id', '=', intval(input('get.a_domain_id'))];
        }
        $list = Sites::getPageList($where);
        $siteStatus = lang('siteStatus');
        $areas = lang('area');
        foreach ($list as &$item) {
            $item->aDomain = empty($item->domains) ? '' : $item->domains->domain;
            $item->webDomains = empty($item->webDomains) ? [] : $item->webDomains->column('domain');
        }
        $domains = Domains::field('id, domain')->select()->column(null, 'id');

        return $this->view('list', compact('list', 'siteStatus', 'domains', 'areas'));
    }

    public function changeWebDomains()
    {
        if (!$this->permissions()) {
            return message('无权操作');
        }
        $site = $this->model->getById(intval(input('get.id')));
        if (!$site->isExists()) {
            return message('The data does not exist');
        }
        if ($site->deployed == 0) {
            return message('此站点未部署');
        }
        if ($site->webDomains->isEmpty()) {
            return message('此站点无关联域名');
        }
        $servers = '';
        // Nginx配置文件目录
        if (!is_dir(runtime_path("nginx/{$site->flag}"))) {
            mkdir(runtime_path("nginx/{$site->flag}"));
        }
        $frontendConfPath = runtime_path("nginx/{$site->flag}") . 'frontend.conf';
        // 查询部署记录
        $deploys = Deploy::where('site_id', $site->id)->select();
        // 将后端部署host，站点ID，站点域名写入Nginx配置文件
        foreach ($deploys as $deploy) {
            if ($deploy->server_type == 1) {
                $servers .= "server {$deploy->private_domain};" . PHP_EOL;
            }
        }
        $webDomains = $site->webDomains->column('domain');
        foreach ($webDomains as $webDomain) {
            $webDomains[] = "www.{$webDomain}";
        }
        $webDomains[] = $site->backend_domain;
        $frontendConf = lang('frontend nginx conf', [
            'siteId' => $site->id,
            'domains' => implode(' ', $webDomains),
            'servers' => $servers,
        ]);
        file_put_contents($frontendConfPath, $frontendConf);
        $exec = [];
        foreach ($deploys as $deploy) {
            if ($deploy->server_type == 2) {
                $exec[] = shell_exec("scp {$frontendConfPath} root@{$deploy->servers->public_ip}:/etc/nginx/conf.d/{$site->flag}.conf");
                $exec[] = shell_exec("ssh root@{$deploy->servers->public_ip} \"nginx -s reload\"");
            }
        }

        return message(json_encode($exec));
    }

    public function delete()
    {
        if (!$this->permissions()) {
            return message('无权操作');
        }
        $domains = Domains::where('site_id', intval(input('get.id')))->select();
        foreach ($domains as $domain) {
            if (!empty($domain->zone_identifier)) {
                // 先删dns记录
                $records = Records::where('domain_id', $domain->id)->select();
                if (!$records->isEmpty()) {
                    foreach ($records as $record) {
                        if (!empty($record->identifier)) {
                            CfServer::instance()->delDns($domain->zone_identifier, $record->identifier);
                        }
                        Records::destroy($record->id);
                    }
                }
                CfServer::instance()->delZone($domain->zone_identifier);
            }
            Domains::destroy($domain->id);

        }
        Deploy::where('site_id', intval(input('get.id')))->delete();

        return parent::delete();
    }

}
