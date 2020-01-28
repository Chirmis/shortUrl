<?php
namespace app\admin\model;

use think\Model;

class Link extends Model
{
    public function user()
    {
        return $this->belongsTo('User', 'uid', 'id');
    }
}