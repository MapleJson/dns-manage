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
    Route::put('server/update', 'server/update')->name('server-update');
    Route::get('server/delete', 'server/delete')->name('server-delete');

    // 域名管理
    Route::get('domain/index', 'domain/index')->name('domain-index');
    Route::post('domain/create', 'domain/create')->name('domain-create');
    Route::post('domain/save', 'domain/save')->name('domain-save');
    Route::put('domain/update', 'domain/update')->name('domain-update');

    // DNS记录
    Route::get('dns/index', 'dns/index')->name('dns-index');
    Route::get('dns/create', 'dns/create')->name('dns-create');
    Route::post('dns/save', 'dns/save')->name('dns-save');
    Route::put('dns/update', 'dns/update')->name('dns-update');

    // 管理员管理
    Route::get('admin/index', 'admin/index')->name('admin-index');
    Route::post('admin/create', 'admin/save')->name('admin-create');
    Route::put('admin/update', 'admin/update')->name('admin-update');
    Route::get('admin/reset', 'admin/reset')->name('admin-reset');

    // 个人信息
    Route::get('user/profile', 'user/index')->name('user-profile');
    Route::put('user/password', 'user/update')->name('user-password');





})->middleware('auth');