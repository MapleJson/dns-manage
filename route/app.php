<?php

use think\facade\Route;

Route::get('login', 'login/login')->name('login');
Route::post('login/do', 'login/loginDo')->name('login-do');

// 需要走登录验证
Route::group(function () {

    Route::get('/', 'index/index')->name('home');

    //登出
    Route::get('logout', 'login/logout')->name('logout');

    // 服务器管理
    Route::get('server/index', 'server/index')->name('server-index');
    Route::post('server/create', 'server/save')->name('server-create');
    Route::post('server/update', 'server/update')->name('server-update');
    Route::get('server/delete', 'server/delete')->name('server-delete');

    // 域名管理
    Route::get('domain/index', 'domain/index')->name('domain-index');
    Route::post('domain/create', 'domain/save')->name('domain-create');
    Route::post('domain/update', 'domain/update')->name('domain-update');
    Route::get('domain/delete', 'domain/delete')->name('domain-delete');

    // DNS记录
    Route::get('record/index', 'record/index')->name('record-index');
    Route::post('record/create', 'record/save')->name('record-create');
    Route::get('record/delete', 'record/delete')->name('record-delete');

    // 站点管理
    Route::get('site/index', 'site/index')->name('site-index');
    Route::post('site/create', 'site/save')->name('site-create');
    Route::post('site/update', 'site/update')->name('site-update');
    Route::get('site/deploy', 'site/deploy')->name('site-deploy');
    Route::post('site/change/domain', 'site/changeWebDomains')->name('site-change-domain');
    Route::get('site/delete', 'site/delete')->name('site-delete');

    // 管理员管理
    Route::get('admin/index', 'admin/index')->name('admin-index');
    Route::post('admin/create', 'admin/save')->name('admin-create');
    Route::get('admin/update', 'admin/update')->name('admin-update');
    Route::get('admin/reset', 'admin/reset')->name('admin-reset');

    // 个人信息
    Route::get('user/profile', 'user/index')->name('user-profile');
    Route::put('user/password', 'user/update')->name('user-password');





})->middleware('auth');