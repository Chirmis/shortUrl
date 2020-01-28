<?php
namespace app\index\controller;

use think\facade\Cookie;
use think\Request;

class Login extends Base
{
    protected $beforeActionList = [
        'checkLogin'  =>  ['only'=>'login'],
    ];

    public function checkLogin()
    {
        $userToken = Cookie::get('user_accesstoken');
        if ($userToken != '') {
            return $this->error("您已登录账号，如需登陆其他账号，请退出当前账号", '/');
        }
    }

    public function login(Request $request)
    {
        if($request->isAjax()){
            $postData = $request->post();
            if (empty($postData['captcha']) || !array_key_exists('captcha', $postData)) {
                return json(['code' => 0, 'msg' => "请输入验证码"]);
            }
            $res = db('user')->where('username', $postData['username'])->find();
            if (empty($res) || ($res['password'] !== md5(sha1($postData['password']).md5($res['salt'])))) {
                return json(['code' => 0, 'msg' => "用户名或密码错误"]);
            }
            if ($res['status'] != 1) {
                return json(['code' => 0, 'msg' => "该账户被系统禁用"]);
            }
            db('user')->where('username',$postData['username'])->setInc('logins');
            db('user')->where('username',$postData['username'])->update(['lastlogintime' => date('Y-m-d H:i:s')]);
            Cookie::set('user_accesstoken', $res['accesstoken']);
            Cookie::set('timestamp', time());
            return json(['code' => 1, 'msg' => "登陆成功！", 'url'=>url('/')]);
        };
        return $this->fetch();
    }

    public function logout()
    {
        if (Cookie::get('user_accesstoken') != '') {
            Cookie::delete('user_accesstoken');
            return $this->success("退出成功",'/');
        }else {
            return $this->error("你没登录，退出nmlgb呢?", '/');
        }
    }
}