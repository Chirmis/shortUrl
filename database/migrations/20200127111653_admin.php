<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Admin extends Migrator
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
        $table = $this->table('admin', array('engine'=>'MyISAM'))
                      ->setCollation("utf8_general_ci")
                      ->setId('id')
                      ->setPrimaryKey('id')
                      ->setComment("管理员表");
        $table->addColumn('username', 'string', [
            'limit'  => 20,
            'null'   => false,
            'comment'=> "用户名"
        ])->addColumn('password', 'string', [
            'length' => 32,
            'null'   => false,
            'comment'=> "密码"
        ])->addColumn('nickname', 'text', [
            'comment' => "昵称"
        ])->addColumn('avatarurl', 'string', [
            'comment' => "头像链接"
        ])->create();
    }
}
