<?php
namespace app\admin\controller;

use think\Controller;
use think\facade\Cookie;

class Login extends Controller
{
    public function index()
    {
        if(cookie('adminInfo') && cookie('adminId')){
            return $this->redirect('/admin');
        }
        return $this->fetch();
    }

    public function login()
    {
        if (!request()->isAjax()) {
            return json([
                'code' => 0,
                'msg'  => "非法操作"
            ], 200);
        }
        $postData = request()->post();
        if (empty($postData['captcha']) || !array_key_exists('captcha', $postData)) {
            return json(['code' => 0, 'msg' => "请输入验证码"]);
        }
        $adminInfo = db('admin')->where('username', $postData['username'])->find();
        if (md5(sha1($postData['password'])) != $adminInfo['password'] || $adminInfo == null) {
            return json(['code' => 0, 'msg' => "账号或密码不正确"]);
        }
        Cookie::set('sys_time', time());
        Cookie::set('adminInfo', strtoupper(sha1(json_encode($adminInfo))));
        Cookie::set('adminId', $adminInfo['id']);
        return json(['code' => 1, 'msg' => "登陆成功！", 'url' => url('/admin')]);
    }
}