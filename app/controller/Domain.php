<?php
declare (strict_types=1);

namespace app\controller;

use app\AdminController;
use app\model\Domains;
use app\model\Sites;

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
        }
        $sites = Sites::field('id, site_name')->select()->column(null, 'id');
        return $this->view('list', compact('list', 'sites'));
    }

    public function save()
    {
        $result = curl_http(
            "https://api.cloudflare.com/client/v4/zones",
            'POST',
            [
                'type' => 'full',
                'name' => input('post.domain'),
                'account' => [
                    'id' => env('cf.account_id')
                ]
            ],
            [
                'Authorization: Bearer ' . env('cf.auth_key'),
                'Content-Type: application/json'
            ]
        );
        $domain = json_decode($result, true);
        if ($domain['success']) {
            request()->withPost(['zone_identifier' => $domain['result']['id']]);
            return parent::save();
        }
        return message('Submission Failed');
    }

}
