<?php
namespace app\index\controller;

use think\facade\Cache;
use think\facade\Cookie;

class Index extends Base
{
    public function index()
    {
        $userToken = Cookie::get('user_accesstoken');
        if ($userToken != '') {
            $userInfo = db('user')->where('accesstoken', $userToken)->field('id,username,email,nickname,accesstoken')->find();
        }else {
            $userInfo = '';
        }
        $this->assign('userInfo', $userInfo);
        return $this->fetch();
    }

    public function getName()
    {
        $queryData = request()->param();
        $code = $queryData['name'];
        //优先取缓存
        $cacheText = Cache::get($code);
        if ($cacheText && $cacheText != 'null') {
            $data = json_decode($cacheText, true);
            if (is_array($data)) {
                $originalurl = $data['originalurl'];
                if (array_key_exists("id", $data)) {
                    //存在ID则为用户数据缓存,否则为未登录状态缓存
                    db('link')->where('id', $data['id'])->setInc('click');
                }
                $originalurl = urldecode($originalurl);
                //判断跳转类型
                if ($data['jumptype'] != 0) {
                    return $this->redirect("/safeJump?type=".$data['jumptype']."&url=$originalurl");
                }else {
                    return $this->redirect($originalurl, 302);
                }
            }else {
                return $this->redirect("/", 301);
            }
        }
        //无缓存查数据
        $linkInfo = db('link')->where('code', $code)->find();
        if ($linkInfo && is_array($linkInfo)) {
            //非永久储存判断是否过期
            if ($linkInfo['effectivetime'] != 0) {
                if (strtotime($linkInfo['createtime']) + $linkInfo['effectivetime'] < time()) {
                    if (db('link')->where('code', $code)->delete() !== false) {
                        return $this->redirect("/", 301);
                    }
                }
            }
            //未过期先写缓存
            $chcheText = json_encode($linkInfo);
            Cache::set($code, $chcheText, $linkInfo['effectivetime']);
            db('link')->where('id', $linkInfo['id'])->setInc('click');
            $originalurl = urldecode($linkInfo['originalurl']);
            //判断跳转类型
            if ($linkInfo['jumptype'] != 0) {
                return $this->redirect("/safeJump?type=".$linkInfo['jumptype']."&url=$originalurl");
            }else {
                return $this->redirect($originalurl, 302);
            }
        }
        return $this->redirect("/", 301);
    }

    public function safeJump()
    {
        $queryData = input();
        $this->assign([
            'url'  => $queryData['url'] ? : "https://www.zxit.top/",
            'type' => $queryData['type']
        ]);
        return $this->fetch();
    }
}
