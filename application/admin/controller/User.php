<?php
namespace app\admin\controller;
use app\admin\model\User as UserModel;

class User extends Base
{
    //用户首页
    public function index()
    {
        return $this->fetch();
    }

    public function userList()
    {
        $queryData = request()->get();
        $userInfo = db('user')->where('id','>=',1)->limit($queryData['limit'])->select();
        return json([
            'code' => 1,
            'data' => $userInfo,
            'msg'  => "查询成功",
            'count'=> count($userInfo),
        ],200);
    }

    //更改用户状态
    public function changeUserStatus()
    {
        if (request()->isPut()) {
            $userModel = new UserModel();
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
            $res = $userModel->saveAll($list);
            if ($res !== false) {
                return json(['code'=>1, 'msg'=>'操作成功！'], 200);
            }else {
                return json(['code'=>0, 'msg'=>'操作失败！'], 200);
            }
        }
    }

    //查询指定用户
    public function searchUser()
    {
        $queryData = request()->get();
        $mustParam = ['page','limit','startTime','endTime','username'];
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

        if ($queryData['startTime'] && $queryData['endTime'] && !$queryData['username']) {
            $res = db('user')->where('registertime', 'between time', [$queryData['startTime'], $queryData['endTime']])->select();
        }elseif ($queryData['username']) {
            $res = db('user')->where('username', $queryData['username'])->select();
        }elseif ($queryData['startTime']) {
            $res = db('user')->whereTime('registertime', '>', $queryData['startTime'])->select();
        }elseif ($queryData['endTime']) {
            $res = db('user')->whereTime('registertime', '<', $queryData['endTime'])->select();
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
        return json([
            'code' => 1,
            'msg'  => "查询成功",
            'data' => $res,
            'count'=> count($res),
        ], 200);
    }

    public function delUser()
    {
        if(request()->isDelete()){
            $UserModel = new UserModel();
            $delData = request()->delete();
            $idsStr = rtrim($delData['ids'], ",");
            $idsArr = explode(',', $idsStr);
            if($UserModel::destroy($idsArr) !== false){
                return json(['code'=>1, 'msg'=>'操作成功！'], 200);
            }else {
                return json(['code'=>0, 'msg'=>'操作失败！'], 200);
            }
        }
    }
}