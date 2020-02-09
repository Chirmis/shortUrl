<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

Route::group('/', function(){
    Route::rule('/', 'index');
    Route::rule('/index', 'index');
    Route::rule('user/login', 'index/Login/login');
    Route::rule('user/register', 'index/Register/register');
    Route::rule('user/logout', 'index/Login/logout');
    Route::rule('/safeJump', 'index/index/safeJump');
});

//后台路由组
Route::group('admin', function(){
    //后台首页
    Route::rule('/', 'admin/index/index');
    Route::rule('/main', 'admin/index/main');
    //修改密码
    Route::rule('/editAdmin', 'admin/index/editadmin');
    Route::rule('/changeAdminInfo', 'admin/index/changeAdminInfo');
    //后台登录界面
    Route::rule('/login', 'admin/login/index');
    Route::rule('/adminLogin', "admin/login/login");
    //退出登录
    Route::rule('/logout', "admin/index/logout");
    //系统设置路由组
    Route::group('system', function(){
        Route::rule('/', 'admin/system/index');
    });
    //链接管理路由组
    Route::group('link', function(){
        Route::rule('/', 'admin/link/index');
        Route::rule('/linkList', 'admin/link/linkList');
        Route::rule('/linkSearch', 'admin/link/linkSearch');
        Route::rule('/changeLinkStatus', 'admin/link/changeLinkStatus');
    });

    //用户管理路由组
    Route::group('user', function(){
        Route::rule('/', 'admin/user/index');
        Route::rule('/userList', 'admin/user/userList');
        Route::rule('/searchUser', 'admin/user/searchUser');
        Route::rule('/changeUserStatus', 'admin/user/changeUserStatus');
    });
});

// Route::controller('api', 'api/index');
// 接口路由组
Route::group('api', function(){
    Route::rule('/', 'api/index/index');
    Route::rule('/getShortUrl', 'api/index/getshorturl');
    Route::rule('/getOriginalUrl', 'api/index/getOriginalUrl');
});
//最低优先级
Route::rule('/:name', 'index/getName');