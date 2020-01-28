<?php

use think\migration\Migrator;
use think\migration\db\Column;

class User extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('user',array('engine'=>'MyISAM'))
                      ->setCollation("utf8_general_ci")
                      ->setId('id')
                      ->setPrimaryKey('id')
                      ->setComment("用户表");
        $table->addColumn('username', 'string', [
            'length'  => 25,
            'null'    => false,
            'comment' => "用户名",
        ])->addColumn('password', 'string', [
            'length'  => 32,
            'null'    => false,
            'comment' => "密码",
        ])->addColumn('nickname', 'text', [
            'null'    => false,
            'comment' => "昵称",
        ])->addColumn('salt', 'string', [
            'length' => 8,
            'null'   => false,
            'comment' => "密码盐"
        ])->addColumn('email', 'string', [
            'length' => 255,
            'null'   => false,
            'comment' => "邮箱"
        ])->addColumn('status', 'integer', [
            'length' => 1,
            'null'   => false,
            'default'=> 1,
            'comment' => "状态"
        ])->addColumn('accesstoken', 'string', [
            'length' => 64,
            'comment' => "用户密匙"
        ])->addColumn('registertime', 'datetime', [
            'comment' => "注册时间"
        ])->addColumn('lastlogintime', 'datetime', [
            'comment' => "最后登录时间"
        ])->addColumn('logins', 'integer', [
            'signed'  => true,
            'comment' => "登陆次数",
            'length'  => 10
        ])->create();
    }
}
