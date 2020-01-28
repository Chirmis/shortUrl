<?php

use think\migration\Migrator;
use think\migration\db\Column;
use think\facade\Env;

class IniAdmin extends Migrator
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
    public function up()
    {   
        $password = getRandStr(6);
        $data = [
            'id'       => 1,
            'username' => 'root',
            'password' => md5(sha1($password)),
            'nickname' => config('shorturl.nickname'),
            'avatarurl'=> "http://q1.qlogo.cn/g?b=qq&nk=" . config('shorturl.QQnumber') . "&s=100",
        ];
        $this->table('admin')->insert($data)->saveData();
        $lockFile = Env::get('app_path') . 'install' . DIRECTORY_SEPARATOR . 'lock.ini';
        file_put_contents($lockFile, "username:root, password:{$password}");
    }
}
