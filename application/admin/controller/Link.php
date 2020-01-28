<?php

namespace app\admin\controller;
use app\admin\model\Link as LinkModel;

class Link extends Base
{
    public function index()
    {
        return $this->fetch();
    }

    public function linkList()
    {
        $queryData = request()->param();
        $LinkModel = new LinkModel();
        $linkData = $LinkModel::where('id', '>=', 1)->limit(20)->select();
        for ($i=0; $i < count($linkData); $i++) {
            $linkData[$i]['originalurl'] = urldecode($linkData[$i]['originalurl']);
            //获取每条链接所属用户信息
            $linkBelongsToUserInfo = $linkData[$i]->user;
        }
        return json([
            'code' => 1,
            'msg'  => "获取成功",
            'data' => $linkData
        ]);
    }
    
    public function linkSearch()
    {
        $queryData = request()->param();
        $mustParam = ['page','limit','startTime','endTime','code'];
        foreach ($mustParam as $k => $v) {
            if(!array_key_exists($v, $queryData)){
                return json([
                    'code' => 0,
                    'msg'  => "参数错误",
                    'data' => '',
                    'count'=> 0
                ],200);
            }
        }
        foreach ($queryData as $k => $v) {
            $queryData[urldecode($k)] = urldecode($v);
        }
        $LinkModel = new LinkModel();
        if ($queryData['startTime'] && $queryData['endTime'] && !$queryData['code']) {
            $res = $LinkModel::where('createtime', 'between time', [$queryData['startTime'], $queryData['endTime']])->select();
        }elseif ($queryData['code']) {
            $res = $LinkModel::where('code', $queryData['code'])->select();
        }elseif ($queryData['startTime']) {
            $res = $LinkModel::whereTime('createtime', '>', $queryData['startTime'])->select();
        }elseif ($queryData['endTime']) {
            $res = $LinkModel::whereTime('createtime', '<', $queryData['endTime'])->select();
        }else {
            $res = "";
        }
        if ($res == '') {
            return json([
                'code' => 0,
                'msg'  => "条件不足无法查询",
                'data' => '',
                'count'=> 0
            ], 200);
        }
        for ($i=0; $i < count($res); $i++) { 
            $res[$i]['originalurl'] = urldecode($res[$i]['originalurl']);
            $linkBelongsToUserInfo = $res[$i]->user;
        }
        return json([
            'code' => 1,
            'msg'  => "查询成功",
            'data' => $res,
            'count'=> count($res),
        ], 200);
        dump($queryData);
    }

    public function changeLinkStatus()
    {
        if (request()->isPut()) {
            $LinkModel = new LinkModel();
            $putData = request()->put();
            if ($putData['status'] == 1) {
                $statusCode = 1;
            }else {
                $statusCode = 0;
            }
            $idsStr = rtrim($putData['ids'], ",");
            $idsArr = explode(',', $idsStr);
            $list = array();
            for ($i=0; $i < count($idsArr); $i++) { 
                $list[] = ['id' => $idsArr[$i], 'status'=> $statusCode];
            }
            $res = $LinkModel->saveAll($list);
            if ($res !== false) {
                return json(['code'=>1, 'msg'=>'操作成功！'], 200);
            }else {
                return json(['code'=>0, 'msg'=>'操作失败！'], 200);
            }
        }
    }
}