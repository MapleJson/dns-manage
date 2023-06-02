<?php
namespace app\controller;

use app\AdminController;
use app\model\Records;
use app\model\Servers;
use app\model\Sites;
use app\service\CfServer;

class Deploy extends AdminController
{
    public function index()
    {
        $where = [];
        if (!empty(input('get.site_id'))) {
            $where[] = ['site_id', '=', intval(input('get.site_id'))];
        }
        $list = \app\model\Deploy::getPageList($where);
        $servers = Servers::field('id, server_name, type')->select()->column(null, 'id');
        $sites = Sites::field('id, site_name')->select()->column(null, 'id');
        $type = lang('type');
        return $this->view('list', compact('list', 'servers', 'sites', 'type'));
    }

    public function deploy()
    {
        if (!$this->permissions()) {
            return message('无权操作');
        }
        $site = Sites::getById(intval(input('post.site_id')));
        if (!$site->isExists()) {
            return message('The data does not exist');
        }
        if ($site->deployed == 1) {
            return message('此站点已部署过');
        }
        if (empty(input('post.back_ids'))) {
            return message('请选择后端服务器');
        }
        if (empty(input('post.front_ids'))) {
            return message('请选择节点服务器');
        }
        $backIds = input('post.back_ids');
        $frontIds = input('post.front_ids');
        // 程序依赖安装
        shell_exec("cd {$site->base_path} && composer install");
        // Nginx配置文件目录
        if (!is_dir(runtime_path("nginx/{$site->flag}"))) {
            mkdir(runtime_path("nginx/{$site->flag}"));
        }
        $frontendConfPath = runtime_path("nginx/{$site->flag}") . 'frontend.conf';
        $servers = Servers::field('id, public_ip, private_ip, type')->select()->column(null, 'id');
        $records = $execs = $deploys = [];
        $confServers = $hosts = '';
        foreach ($backIds as $backId) {
            $random = $site->flag . substr(md5($site->site_name . uuid()), 0, 10);
            $records[] = $random;
            $deploys[] = [
                'site_id' => $site->id,
                'private_domain' => $random,
                'server_id' => intval($backId),
                'server_type' => 1,
            ];

            $backendConf = lang('backend nginx conf', [
                'random' => $random,
                'originPath' => $site->origin_path,
            ]);
            $backendConfPath = runtime_path("nginx/{$site->flag}") . "{$random}.conf";
            file_put_contents($backendConfPath, $backendConf);
            // 传输Nginx配置文件命令
            $execs[] = "scp {$backendConfPath} root@{$servers[$backId]['public_ip']}:/etc/nginx/conf.d/{$random}.conf";
            // 发布代码
            $execs[] = "/usr/bin/rsync -vzrtopg --omit-dir-times --delete --exclude \".git\" --exclude \".gitignore\" --exclude \".env\" --exclude \"runtime\" {$site->base_path}/ root@{$servers[$backId]['public_ip']}:{$site->origin_path}/";
            // 远程执行后端服务器命令
            $execs[] = "ssh root@{$servers[$backId]['public_ip']} \"chown nginx.nginx {$site->origin_path} -R;nginx -s reload\"";
            // 节点服务器的hosts
            $hosts .= "{$servers[$backId]['public_ip']}      {$random}" . PHP_EOL;
            $confServers .= "server {$random};" . PHP_EOL;
        }
        // 节点Nginx配置域名
        $webDomains = $site->webDomains->column('domain');
        foreach ($webDomains as $webDomain) {
            $webDomains[] = "www.{$webDomain}";
        }
        // 后台域名前缀
        $adminRecord = "admin{$site->flag}" . array_rand($records);
        // 添加后台的A记录至节点的域名
        $backendDns = CfServer::instance()->addDns($site->domains->zone_identifier, [
            'name' => $adminRecord,
            'content' => $servers[array_rand($frontIds)]['public_ip'],
            'comment' => $site->site_name . '后台',
        ]);
        if ($backendDns['success']) {
            Records::create([
                'domain_id' => $site->a_domain_id,
                'site_id' => $site->id,
                'type' => $backendDns['result']['type'],
                'name' => $backendDns['result']['name'],
                'content' => $backendDns['result']['content'],
                'comment' => $backendDns['result']['comment'],
                'identifier' => $backendDns['result']['id'],
            ]);
            $site->backend_domain = $backendDns['result']['name'];
            $webDomains[] = $backendDns['result']['name'];
        }
        // 节点Nginx配置文件
        $frontendConf = lang('frontend nginx conf', [
            'siteId' => $site->id,
            'domains' => implode(' ', $webDomains),
            'servers' => $confServers,
        ]);
        file_put_contents($frontendConfPath, $frontendConf);
        // 添加前台的A记录至节点的域名
        foreach ($frontIds as $frontId) {
            $frontendDns = CfServer::instance()->addDns($site->domains->zone_identifier, [
                'name' => array_rand($records),
                'content' => $servers[$frontId]['public_ip'],
                'comment' => $site->site_name . '前台',
            ]);
            if ($frontendDns['success']) {
                Records::create([
                    'domain_id' => $site->a_domain_id,
                    'site_id' => $site->id,
                    'type' => $frontendDns['result']['type'],
                    'name' => $frontendDns['result']['name'],
                    'content' => $frontendDns['result']['content'],
                    'comment' => $frontendDns['result']['comment'],
                    'identifier' => $frontendDns['result']['id'],
                ]);
            }
            $deploys[] = [
                'site_id' => $site->id,
                'server_id' => intval($frontId),
                'server_type' => 2,
            ];
            $execs[] = "scp {$frontendConfPath} root@{$servers[$frontId]['public_ip']}:/etc/nginx/conf.d/{$site->flag}.conf";
            $execs[] = "ssh root@{$servers[$frontId]['public_ip']} \"echo '{$hosts}' >> /etc/hosts;nginx -s reload\"";
        }
        $site->deployed = 1;
        $site->save();
        \app\model\Deploy::saveAll($deploys);

        $execRes = [];
        foreach ($execs as $exec) {
            $execRes[] = shell_exec($exec);
        }
        halt($execs);
        return message(json_encode($execRes));
    }
}
