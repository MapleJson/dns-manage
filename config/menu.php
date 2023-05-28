<?php

return [
    [
        'name' => '主页',
        'url' => url('home'),
        'icon' => 'fa-home',
    ],
    [
        'name' => '服务器管理',
        'url' => url('server-index'),
        'icon' => 'fa-tachometer-alt',
    ],
    [
        'name' => '域名管理',
        'url' => url('domain-index'),
        'icon' => 'fa-users',
    ],
    [
        'name' => 'DNS管理',
        'url' => url('dns-index'),
        'icon' => 'fa-users',
    ],

    [
        'name' => '管理员管理',
        'url' => url(''),
        'icon' => 'fa-users-cog',
        'children' => [
            [
                'name' => '管理员列表',
                'url' => url('admin-index'),
                'icon' => '',
            ],
//            [
//                'name' => '操作日志',
//                'url' => url('admin-log'),
//                'icon' => '',
//            ],
        ]
    ],
];