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
            $item->aBackDns = empty($item->backRecords) ? '' : $item->backRecords->name;
            $item->aFrontDns = empty($item->frontRecords) ? '' : $item->frontRecords->name;
            $item->webDomains = explode(' ', $item->web_domains);
        }
        $domains = Domains::field('id, domain')->select()->column(null, 'id');
        $servers = Servers::field('id, server_name')->where('type', 1)->select()->column(null, 'id');
        $frontServers = Servers::field('id, server_name')->where('type', 2)->select()->column(null, 'id');
        return $this->view('list', compact('list', 'siteStatus', 'domains', 'servers', 'frontServers'));
    }

    public function deploy()
    {
        if (!$this->permissions()) {
            return message('无权操作');
        }
        $site = $this->model->getById(intval(input('get.id')));
        if (!$site->isExists()) {
            return message('The data does not exist');
        }
        if ($site->deployed == 1) {
            return message('此站点已部署过');
        }
        $random = substr(md5($site->site_name . uuid()), 0, 8);
        $adminDomain = "admin{$random}";
        $frontDomain = "front{$random}";
        $backendConf =
<<<EOF
server {
        listen       80;
    server_name  {$random} {$adminDomain}.{$site->domains->domain};
    root         "{$site->origin_path}/public";
    index index.php index.html;

    location ~* (runtime|application)/{
            return 403;
        }
    location / {
            if (!-e \$request_filename){
                rewrite  ^(.*)$  /index.php?s=\$1 last; break;
        }
    }
    location ~ \.php(.*)$ {
        fastcgi_hide_header X-Powered-By;
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_split_path_info  ^((?U).+\.php)(/?.+)$;
        fastcgi_param  SCRIPT_FILENAME  \$document_root\$fastcgi_script_name;
        fastcgi_param  PATH_INFO  \$fastcgi_path_info;
        fastcgi_param  PATH_TRANSLATED  \$document_root\$fastcgi_path_info;
        include        fastcgi_params;
    }
    access_log  /var/log/nginx/{$random}.log;
    error_log  /var/log/nginx/{$random}.error.log;
}
EOF;

        // 写Nginx配置文件
        if (!is_dir(runtime_path("nginx/{$random}"))) {
            mkdir(runtime_path("nginx/{$random}"));
        }
        $backendConfPath = runtime_path("nginx/{$random}") . 'backend.conf';
        file_put_contents($backendConfPath, $backendConf);
        // 传输Nginx配置文件命令
        $scpBack = "scp {$backendConfPath} root@{$site->servers->public_ip}:/etc/nginx/conf.d/{$random}.conf";
        // 程序依赖安装
        $install = "cd {$site->base_path} && composer install";
        // 发布代码
        $rsync = "/usr/bin/rsync -vzrtopg --omit-dir-times --delete --exclude \".git\" --exclude \".gitignore\" --exclude \".env\" --exclude \"runtime\" {$site->base_path}/ root@{$site->servers->public_ip}:{$site->origin_path}/";
        // 远程执行后端服务器命令
        $backSsh = "ssh root@{$site->servers->public_ip} \"chown nginx.nginx {$site->origin_path} -R;nginx -s reload\"";
        // 判断是否同一内网  远程执行节点服务器命令
        $backIps = explode('.', $site->servers->private_ip);
        $frontIps = explode('.', $site->frontServers->private_ip);
        if ("{$backIps[0]}{$backIps[1]}{$backIps[2]}" === "{$frontIps[0]}{$frontIps[1]}{$frontIps[2]}") {
            $frontSsh = "ssh root@{$site->frontServers->public_ip} \"echo '{$site->servers->private_ip}      {$random}' >> /etc/hosts;nginx -s reload\"";
        } else {
            $frontSsh = "ssh root@{$site->frontServers->public_ip} \"echo '{$site->servers->public_ip}      {$random}' >> /etc/hosts;nginx -s reload\"";
        }

        $backResult = curl_http(
            "https://api.cloudflare.com/client/v4/zones/{$site->domains->zone_identifier}/dns_records",
            'POST',
            [
                'type' => 'A',
                'name' => $adminDomain,
                'content' => $site->servers->public_ip,
                'comment' => $site->site_name . '后台',
                'proxied' => true,
            ],
            [
                'Authorization: Bearer ' . env('cf.auth_key'),
                'Content-Type: application/json'
            ]
        );
        $frontResult = curl_http(
            "https://api.cloudflare.com/client/v4/zones/{$site->domains->zone_identifier}/dns_records",
            'POST',
            [
                'type' => 'A',
                'name' => $frontDomain,
                'content' => $site->frontServers->public_ip,
                'comment' => $site->site_name . '前台',
                'proxied' => true,
            ],
            [
                'Authorization: Bearer ' . env('cf.auth_key'),
                'Content-Type: application/json'
            ]
        );
        $backendDns = json_decode($backResult, true);
        $frontendDns = json_decode($frontResult, true);
        if ($backendDns['success']) {
            $backRecord = Records::create([
                'domain_id' => $site->a_domain_id,
                'site_id' => $site->id,
                'type' => $backendDns['result']['type'],
                'name' => $backendDns['result']['name'],
                'content' => $backendDns['result']['content'],
                'comment' => $backendDns['result']['comment'],
                'identifier' => $backendDns['result']['id'],
            ]);
            $site->backend_domain = $backendDns['result']['name'];
            $site->back_a_record_id = $backRecord->id;
        }
        if ($frontendDns['success']) {
            $frontRecord = Records::create([
                'domain_id' => $site->a_domain_id,
                'site_id' => $site->id,
                'type' => $frontendDns['result']['type'],
                'name' => $frontendDns['result']['name'],
                'content' => $frontendDns['result']['content'],
                'comment' => $frontendDns['result']['comment'],
                'identifier' => $frontendDns['result']['id'],
            ]);
            $site->front_a_record_id = $frontRecord->id;
        }
        $site->private_domain = $random;
        $site->deployed = 1;
        $site->save();

        $exec = [
            'scpBack' => shell_exec($scpBack),
            'install' => shell_exec($install),
            'rsync' => shell_exec($rsync),
            'backSsh' => shell_exec($backSsh),
            'frontSsh' => shell_exec($frontSsh),
        ];
        halt($exec);
    }

    public function changeWebDomains()
    {
        if (!$this->permissions()) {
            return message('无权操作');
        }
        $site = $this->model->getById(intval(input('post.id')));
        if (!$site->isExists()) {
            return message('The data does not exist');
        }
        $site->web_domains = trim(input('post.web_domains'));
        $site->save();
        $frontendConf =
<<<EOF
server {
    listen       80;
    server_name  {$site->web_domains};
    location / {
        proxy_pass http://{$site->private_domain};
        proxy_set_header X-Read-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
    }
    access_log  /var/log/nginx/{$site->private_domain}.log;
    error_log  /var/log/nginx/{$site->private_domain}.error.log;
}
EOF;
        // 写Nginx配置文件
        if (!is_dir(runtime_path("nginx/{$site->private_domain}"))) {
            mkdir(runtime_path("nginx/{$site->private_domain}"));
        }
        $frontendConfPath = runtime_path("nginx/{$site->private_domain}") . 'frontend.conf';
        file_put_contents($frontendConfPath, $frontendConf);
        $scpFront = "scp {$frontendConfPath} root@{$site->frontServers->public_ip}:/etc/nginx/conf.d/{$site->private_domain}.conf";

        $exec = [
            'scpFront' => shell_exec($scpFront),
        ];
        return message(json_encode($exec));
    }

}
