<?php

return [
    'Loginfailure'                                => '登录失败次数',
    'Login time'                                  => '最后登录',
    'Please input correct Username'               => '用户名只能由3-12位数字、字母、下划线组合',
    'Please input correct Password'               => '密码长度必须在6-16位之间，不能包含空格',
    'Username or password is incorrect'           => '用户名或密码不正确',
    'Username is incorrect'                       => '用户名不正确',
    'Password is incorrect'                       => '密码不正确',
    'Admin is forbidden'                          => '管理员已经被禁止登录',
    'Agent is forbidden'                          => '你已经被禁止登录',
    'Please try again after 1 day'                => '请于1天后再尝试登录',
    'Illegal request'                             => '非法请求',
    'Successfully change password'                => '密码修改成功',
    'Password change failed'                      => '密码修改失败',

    'Submission successfully'                      => '提交成功',
    'Deploy successfully'                         => '部署成功,请前往执行脚本',
    'Submission Failed'                           => '提交失败',
    'Delete successfully'                         => '删除成功',
    'Delete Failed'                               => '删除失败',
    'Edit successfully'                           => '修改成功',
    'Edit Failed'                                 => '修改失败',
    'Exec successfully'                           => '执行成功',
    'Exec Failed'                                 => '执行失败',

    'The data does not exist'                     => '该条数据不存在',
    'The admin does not exist'                    => '该管理员不存在',
    'Admin modified successfully'                 => '管理员修改成功',
    'Admin modification failed'                   => '管理员修改失败',
    'Rsync successfully'                          => '请前往命令管理页面执行命令',
    'backend nginx conf'                          => <<<EOF
server {
    listen       {:port};
    server_name  {:publicIp} {:privateIp};
    root         "{:originPath}/public";
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
    access_log  /var/log/nginx/{:flag}.log;
    error_log  /var/log/nginx/{:flag}.error.log;
}
EOF,

    'frontend nginx conf' => <<<EOF
upstream balanced{:flag} {
    ip_hash;{:servers}
}
server {
    listen       80;
    server_name  {:domains};
    location / {
        proxy_pass http://balanced{:flag};
        proxy_set_header X-Read-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        client_max_body_size       8m;		#允许客户端请求的最大单文件字节数
        client_body_buffer_size    128k;	#缓冲区代理缓冲用户端请求的最大字节数
        proxy_connect_timeout      300;		#nginx跟后端服务器连接超时时间(代理连接超时)
        proxy_send_timeout         300;		#后端服务器数据回传时间(代理发送超时)
        proxy_read_timeout         300;		#连接成功后，后端服务器响应时间(代理接收超时)
    }
    access_log  /var/log/nginx/balanced{:flag}.log;
    error_log  /var/log/nginx/balanced{:flag}.error.log;
}
server {
    listen       80;
    server_name  {:adminDomain};
    location / {
        proxy_pass http://balanced{:flag};
        proxy_set_header Host \$host;
        proxy_set_header X-Read-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        client_max_body_size       8m;		#允许客户端请求的最大单文件字节数
    }
    access_log  /var/log/nginx/balanced{:flag}admin.log;
    error_log  /var/log/nginx/balanced{:flag}admin.error.log;
}
EOF,

    'useStatus' => [
        1 => '正常',
        2 => '停用',
    ],

    'type' => [
        1 => '后端',
        2 => '节点',
    ],

    'siteStatus' => [
        1 => '开发中',
        2 => '准备好',
        3 => '已上线',
        4 => '已下线',
    ],

    'area' => [
        1 => '尼日',
        2 => '菲律宾',
        3 => '印尼',
        4 => '埃及',
        5 => '越南',
        6 => '泰国',
        7 => '南非',
        8 => '印度',
        9 => '其他',
    ],

];
