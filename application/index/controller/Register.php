<?php

namespace app\index\controller;

use think\Request;
use think\Validate;

class Register extends Base
{
    public function register(Request $request)
    {
        if($request->isAjax()){
            $postData = $request->post();
            $rule = [
                'username'  => 'require|max:20|min:6|unique:user',
                'password'  => 'require',
                'email'     => 'email|unique:user',
                'nickname'  => 'require'
            ];
            $msg = [
                'username.require'  => "用户名必须填写",
                'username.max'      => "用户名不能超过20个字符",
                'username.min'      => "用户名最短为6个字符",
                'username.unique'   => "用户名重复,请重新输入",
                'password.require'  => "密码必填",
                // 'password.alphaNum' => "非法字符,请使用字母和数字正确组合",
                'email.email'       => "邮箱格式不正确",
                'email.unique'      => "邮箱已使用，请更换",
                'nickname.require'  => "昵称必填",
            ];
            $validate = Validate::make($rule, $msg);
            if (!$validate->check($postData)) {
                return json(['code' => 0, 'msg' => $validate->getError()], 200);
            }
            $time = time();
            $salt = getRandStr(8);
            $postData['password'] = md5(sha1($postData['password']).md5($salt));
            $postData['accesstoken'] = getAccessKey(http_build_query($postData));
            $postData['salt'] = $salt;
            $postData['registertime'] = date('Y-m-d H:i:s', $time);
            $postData['lastlogintime'] = date('Y-m-d H:i:s', $time);
            $res = db('user')->insert($postData);
            if($res !== false){
                return json([
                    'code' => 1,
                    'msg'  => "注册成功",
                    'url'  => url('/user/login'),
                ], 200);
            }else {
                return json(['code' => 0, 'msg' => "服务器故障"], 400);
            }
        }
        return $this->fetch();
    }
}