<?php

namespace app\api\controller;

use think\facade\Cache;

class Index
{
    public function index()
    {
        return "Hello,This api for ShortUrl";
    }

    //生成短链接
    public function getShortUrl()
    {
        $queryData = request()->param();
        //判断必要参数
        $siteUrl = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME']."/";
        if (!array_key_exists('url', $queryData) || empty($queryData['url'])) {
            return json(['code' => 0, 'msg' => "参数错误，请传递链接！"]);
        }
        //检测URL格式
        if (!preg_match_all("/^(http|https):\/\/(\w+\.\w.)+(\/|\s)*(.*)/", $queryData['url'])) {
            return json(['code' => 0, 'msg' => "URL格式错误，如有疑问，请联系客服QQ:2457249379！"]);
        }
        //检测时间是否合法
        if (array_key_exists('time', $queryData)) {
            if ($queryData['time'] > 604800 || $queryData['time'] < 0) {
                return json(['code' => 0, 'msg' => "参数错误，请传递规范时间！"]);
            }else {
                $time = $queryData['time'];
            }
        }else {
            $time = 3600;
        }
        //检测跳转类型合法
        if (array_key_exists('type', $queryData)) {
            $map = [0, 1, 2];
            if (in_array($queryData['type'], $map)) {
                $jumptype = $queryData['type'];
            }else{
                return json(['code' => 0, 'msg' => "参数错误，请传递跳转类型！"]);
            }
        }else{
            $jumptype = 0;
        }

        $cookie_token = cookie("user_accesstoken");
        $token = array_key_exists('token', $queryData) ? $queryData['token'] : $cookie_token;
        $code = getShortUrl($queryData['url']);
        //不允许生成的标识符
        switch ($code) {
            case '':
                $code .= "short";
                break;
            case 'index':
                $code .= "short";
                break;
            case 'admin':
                $code .= "short";
                break;
            case 'api':
                $code .= "short";
                break;
        }
        //存数据库
        if ($token && !is_null($token)) {
            $userInfo = db('user')->where('accesstoken', $token)->find();
            //查询无果则为伪造密匙
            if (!$userInfo) {
                return json(['code' => 0, 'msg' => "请输入正确的AccessKey！"]);
            }
            //查看用户状态
            if ($userInfo['status'] === 0) {
                return json(['code' => 0, 'msg' => "该用户被禁用，请联系客服！"]);
            }
            $uid = $userInfo['id'];
            $data = [
                'effectivetime'=> $time,
                'originalurl'  => urlencode($queryData['url']),
                'uid'          => $uid,
                'code'         => $code,
                'createtime'   => date('Y-m-d H:i:s', time()),
                'jumptype'     => $jumptype
            ];
            //避免重复生成如有修改一并更改数据
            $linkArr = db('link')->where('originalurl', urlencode($queryData['url']))->find();
            if($linkArr){
                return $this->upData($linkArr, $data, $siteUrl, true);
            }
            //首次添加则写入数据库
            if (db('link')->insert($data)) {
                return json([
                    'code' => 1, 
                    'msg'  => "链接生成成功,有效期为:". ($time == 0 ? "永久" : $time."秒"),
                    'data' => [
                        'shortUrl'   => $siteUrl . $code,
                        'oldUrl'     => urldecode($queryData['url']),
                        'effectTime' => $time,
                    ],
                ]);
            }else {
                return json([
                    'code' => 0, 
                    'msg'  => "失败，请联系官方QQ：2457249379",
                    'data' => [],
                ]);
            }
        }else {
            //尚未登录存缓存
            //未登录时间不能为永久
            $time = ($time == 0) ? 3600 : $time;
            $time = (int)$time;
            $data = [
                'effectivetime'=> $time,
                'originalurl'  => $queryData['url'],
                'code'         => $code,
                'jumptype'     => $jumptype,
                'createtime'   => date('Y-m-d H:i:s', time()),
            ];
            $old = Cache::get($code);
            if($old && !is_null($old)){
                $linkArr = json_decode($old, true);
                return $this->upData($linkArr, $data, $siteUrl);
            }
            //第一次使用
            $chcheText = json_encode($data);
            Cache::set($code, $chcheText, $time);

            return json([
                'code' => 1, 
                'msg'  => "链接生成成功,有效期为:".$time."秒",
                'data' => [
                    'shortUrl'   => $siteUrl . $code,
                    'oldUrl'     => urldecode($queryData['url']),
                    'effectTime' => $time,
                ],
            ]);
        }
    }

    //还原短链接
    public function getOriginalUrl()
    {
        $queryData = request()->param();
        if (!array_key_exists('shortUrl', $queryData)) {
            return json([
                'code' => 0,
                'msg'  => "参数错误",
                'data' => [],
            ]);
        }
        $siteUrl = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME']."/";
        if (!substr_count($queryData['shortUrl'], $siteUrl)) {
            return json([
                'code' => 0,
                'msg'  => "URL格式错误，或此URL不属于本站！",
                'data' => [],
            ]);
        }
        $code = str_replace($siteUrl, "", $queryData['shortUrl']);
        $cacheText = Cache::get($code);
        //先取缓存
        if ($cacheText && $cacheText != 'null') {
            $data = json_decode($cacheText, true);
            if (is_array($data)) {
                return json([
                    'code' => 1,
                    'msg'  => "操作成功",
                    'data' => [
                        'shortUrl'    => $queryData['shortUrl'],
                        'originalUrl' => urldecode($data['originalurl']),
                    ],
                ]);
            }else {
                return json([
                    'code' => 0,
                    'msg'  => "操作失败",
                    'data' => [],
                ]);
            }
        }
        //无缓存取数据库
        $shortUrlInfo = db('link')->where('code', $code)->find();
        if (is_array($shortUrlInfo)) {
            $cacheText = json_encode($shortUrlInfo);
            $time = time() - strtotime($shortUrlInfo['effectTime']);
            if ($time <= 0) {
                return json([
                    'code' => 0,
                    'msg'  => "此链接已过期！",
                    'data' => [],
                ]);
            }
            Cache::set($code, $cacheText, $time);
            return json([
                'code' => 1,
                'msg'  => "操作成功",
                'data' => [
                    'shortUrl'    => $queryData['shortUrl'],
                    'originalUrl' => urldecode($shortUrlInfo['originalurl']),
                ],
            ]);
        }

        //均无结果，则为错误链接
        return json([
            'code' => 0,
            'msg'  => "URL格式错误，或此URL不属于本站！",
            'data' => [],
        ]);
    }

    private function upData($linkArr, $data, $siteUrl, $login=false)
    {
        if (is_array($linkArr)) {
            $isUpdata = array_merge($linkArr, $data);
            //每次请求时间都不一致，只合并其他不同项，创建时间保留第一次
            $isUpdata['createtime'] = $linkArr['createtime'];
            //如果用户更改了有效期，则需要重新计算
            if ($data['effectivetime'] != $linkArr['effectivetime']) {
                $oldTimeSteamp = strtotime($linkArr['createtime']);
                $nowTimeSteamp = strtotime($data['createtime']);
                $effectivetime = $nowTimeSteamp - $oldTimeSteamp + $data['effectivetime'];
                $isUpdata['effectivetime'] = $effectivetime;
            }
            if($isUpdata['effectivetime'] > 4294967295){
                return json([
                    'code' => 0, 
                    'msg'  => "从创建时间到今已太久，建议登录生成永久链接",
                    'data' => [],
                ]);
            }
            //两次数据不一致则需要更新，否则不需要
            if($linkArr !== $isUpdata){
                if($login){
                    db('link')->where('id', $isUpdata['id'])->update($isUpdata);
                }
                Cache::set($linkArr['code'], json_encode($isUpdata), $isUpdata['effectivetime']);
            }
            return json([
                'code' => 1, 
                'msg'  => "链接生成成功",
                'data' => [
                    'shortUrl'   => $siteUrl . $linkArr['code'],
                    'oldUrl'     => urldecode($data['originalurl']),
                    'effectTime' => $linkArr['effectivetime'],
                ],
            ]);
        }
    }
}