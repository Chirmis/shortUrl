<?php

namespace app\admin\controller;

use think\Controller;

class Base extends Controller
{
    //初始化操作
    public function initialize()
    {
        if (!cookie('adminInfo') || !cookie('adminId')) {
            return $this->error("请先登录!", '/admin/login');
        }
        $adminInfo = db('admin')->where('id', cookie('adminId'))->find();
        if (strtoupper(sha1(json_encode($adminInfo))) != cookie('adminInfo')) {
            cookie('sys_time', null);
            cookie('adminInfo', null);
            cookie('adminId', null);
            return $this->error("非法操作,伪造cookie", '/admin/login');
        }
    }
}