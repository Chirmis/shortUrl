<?php

namespace app\admin\model;

use think\Model;

class User extends Model
{
    public function link()
    {
        return $this->hasMany('Link', 'uid', 'id');
    }
}