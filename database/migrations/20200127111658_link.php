<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Link extends Migrator
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
        $table = $this->table('link', array('engine'=>'MyISAM'))
                      ->setCollation("utf8_general_ci")
                      ->setId('id')
                      ->setPrimaryKey('id')
                      ->setComment("链接表");
        $table->addColumn('code', 'string', [
            'comment' => "短连接码",
            'null'    => false,
        ])->addColumn('uid', 'integer', [
            'comment' => "用户ID",
            'null'    => false,
            'signed'  => false
        ])->addColumn('originalurl', 'string', [
            'comment' => "原链接",
            'null'    => false,
        ])->addColumn('status', 'integer', [
            'default'=> 1,
            'comment' => "链接状态",
            'null'    => false,
            'signed'  => false
        ])->addColumn('effectivetime', 'integer', [
            'comment' => "链接有效时间",
            'null'    => false,
        ])->addColumn('createtime', 'datetime', [
            'comment' => "链接创建时间",
            'null'    => false,
        ])->addColumn('click', 'integer', [
            'comment' => "点击次数",
            'null'    => false,
        ])->create();
    }
}
