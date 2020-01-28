<?php
namespace app\admin\controller;


class Index extends Base
{
    public function index()
    {
        $main_url = url('admin/index/main');
        $this->assign('mainUrl', $main_url);
        if(cookie('adminInfo') && cookie('adminId')){
            $adminInfo = db('admin')->where('id', cookie('adminId'))->find();
            $this->assign('adminInfo', $adminInfo);
            return $this->fetch();
        }else {
            return $this->error('请登录', 'admin/login/index');
        }
    }

    public function main()
    {
        $linkCount = db('link')->count('id');
        $userCount = db('user')->count('id');
        $clickCount = db('link')->count('click');
        $this->assign([
            'linkCount' => $linkCount,
            'userCount' => $userCount,
            'clickCount'=> $clickCount
        ]);
        return $this->fetch();
    }

    public function editAdmin()
    {
        $adminInfo = db('admin')->where('id', cookie('adminId'))->find();
        $this->assign('adminInfo', $adminInfo);
        return $this->fetch();
    }

    public function changeAdminInfo()
    {
        if (!request()->isAjax()) {
            return json(['code' => 0, 'msg' => "非法"]);
        }
        $postData = request()->post();
        if (!array_key_exists('oldPwd', $postData) || !array_key_exists('username', $postData)) {
            return json([
                'code' => 0,
                'msg'  => "缺少必要参数"
            ], 200);
        }
        $password = db('admin')->where('username', $postData['username'])->field('password')->find();
        if (md5(sha1($postData['oldPwd'])) != $password['password']) {
            return json([
                'code' => 0,
                'msg'  => "旧密码错误"
            ], 200);
        }
        if (array_key_exists('newPwd', $postData)) {
            $res = db('admin')->where('username', $postData['username'])->update([
                'password' => md5(sha1($postData['newPwd'])),
            ]);
            if ($res !== false) {
                cookie('sys_time', null);
                cookie('adminInfo', null);
                cookie('adminId', null);
                return json([
                    'code' => 1,
                    "msg"  => "修改成功,请重新登陆！",
                    'url'  => url('/admin/login'),
                ], 200);
            }else {
                return json([
                    'code' => 0,
                    "msg"  => "未知错误，请稍后尝试",
                ], 200);
            }
        }
    }

    public function logout()
    {
        cookie('sys_time', null);
        cookie('adminInfo', null);
        cookie('adminId', null);
        return $this->success('退出成功！', '/admin/login');
    }
}