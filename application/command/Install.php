<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\input\Argument;
use think\console\Output;
use think\db\Connection;
use think\facade\Env;

class Install extends Command
{
    protected function configure()
    {
        $this->setName("install")
             ->addArgument('nickname', null, Argument::REQUIRED, "站长昵称")
             ->addArgument('qq', null, Argument::REQUIRED, "站长QQ")
             ->addOption('db', null, Option::VALUE_REQUIRED, "数据库信息")
             ->setHelp("数据库连接参数，格式为：数据库类型://用户名:密码@数据库地址:数据库端口/数据库名#字符集")
             ->setDescription("ShortUrl安装脚本");
    }

    protected function execute(Input $input, Output $output)
    {
        //定义安装模板根路径
        $tplPath = Env::get('app_path') . 'install' . DIRECTORY_SEPARATOR;
        //检查安装文件是否存在
        $lockFile = $tplPath . 'lock.ini';
        if (file_exists($lockFile)) {
            $output->highlight("您已经安装过了，请勿重新安装！");
            $output->highlight("如有需要请删除application/install/lock.ini文件再次尝试");
            exit;
        }
        //检查写文件权限
        if (!is_writable($tplPath)) {
            $output->highlight($tplPath . '缺少写权限！');
            exit;
        }
        $tempPath = Env::get('runtime_path');
        if (!is_writable($tempPath)) {
            $output->highlight($tempPath . '缺少写权限！');
            exit;
        }
        //检查必要扩展
        if (!extension_loaded('redis')) {
            $output->highlight('缺少Redis扩展！');
            exit;
        }
        //配置数据库
        if ($input->hasOption('db')) {
            try {
                $options = $this->parseDsnConfig($input->getOption('db'));
                Connection::instance($options)->getTables($options['database']);
                $confPath = Env::get('config_path');

                //处理数据库配置文件
                $dbConf = str_replace([
                    '{$DB_TYPE}', '{$DB_HOST}', '{$DB_NAME}',
                    '{$DB_USER}', '{$DB_PASSWORD}', '{$DB_PORT}',
                    '{$DB_CHAR}',
                ], [
                    $options['type'], $options['hostname'], $options['database'],
                    $options['username'], $options['password'], $options['hostport'],
                    $options['charset']
                ], file_get_contents($tplPath . 'database.tpl'));
                file_put_contents($confPath . 'database.php', $dbConf);
                $output->info('数据库配置更新成功');

                //扩展配置
                if ($input->hasArgument('qq') && $input->hasArgument('nickname')) {
                    $qq = $input->getArgument('qq');
                    $nickname = $input->getArgument('nickname');
                    $shorturl = str_replace([
                        '{$QQnumber}', '{$nickname}'
                    ], [
                        $qq, '"' . $nickname . '"'
                    ], file_get_contents($tplPath . 'shorturl.tpl'));
                    file_put_contents($confPath . 'shorturl.php', $shorturl);
                    $output->info('扩展配置更新成功');
                }else {
                    $output->highlight("请输入站长QQ和站长昵称");
                }
                
                //生成lock文件，并且写入用户名密码
                file_put_contents($lockFile, "lock");
                $output->info('lock文件初始化成功');
            } catch (\PDOException $e) {
                $output->highlight($e->getMessage());
            }
        } else {
            $output->highlight("请输入数据库配置");
        }
    }

    /**
     * DSN解析
     * 格式： mysql://username:password@hostname:hostport/DbName?param1=val1&param2=val2#charset
     * @access private
     * @param string $dsnStr
     * @return array
     */
    private function parseDsnConfig($dsnStr) {
        $info = parse_url($dsnStr);

        if (!$info) {
            return [];
        }

        $dsn = [
            'type'     => $info['scheme'],
            'username' => isset($info['user']) ? $info['user'] : '',
            'password' => isset($info['pass']) ? $info['pass'] : '',
            'hostname' => isset($info['host']) ? $info['host'] : '',
            'hostport' => isset($info['port']) ? $info['port'] : '',
            'database' => !empty($info['path']) ? ltrim($info['path'], '/') : '',
            'charset'  => isset($info['fragment']) ? $info['fragment'] : 'utf8',
        ];

        if (isset($info['query'])) {
            parse_str($info['query'], $dsn['params']);
        } else {
            $dsn['params'] = [];
        }

        return $dsn;
    }
}