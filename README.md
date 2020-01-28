# shortUlr短网址系统
### 概述：由[ThinkPHP v5.1.39 LTS](https://packagist.org/packages/topthink/think#v5.1.39 "ThinkPHP v5.1.39 LTS")完成，后台模板来自[ok-admin](https://gitee.com/bobi1234/ok-admin "ok-admin")(GPLv3.0开源协议)
### 前后台展示
![index.PNG](https://i.loli.net/2020/01/28/PNAOrhCMxVz1F7K.png)
![admin.png](https://i.loli.net/2020/01/28/RhAd6UqFSu3BTg7.png)
### 安装使用
首先你需要准备好一台服务器，并安装lnmp/lamp环境(或者直接用宝塔)，安装Redis和php的Redis扩展，否则无法使用
获取代码
```shell
git clone https://github.com/Chirmis/shortUrl.git
```
进入安装目录后，执行安装脚本，给出脚本范例，自行替换
```shell
php think install 昵称 QQ号 --db mysql://用户名:密码@数据库地址:数据库端口/数据库名#utf8
```
注：执行后会有成功提示，会检测目录权限，请根据提示开启部分目录权限，runtime目录必须开启
执行数据库迁移命令
```shell
php think migrate:run
```
以上步骤均完成后，设置public为对外开放目录，如是Nginx则可选择宝塔面板自带thinkPHP伪静态，如是Apache，源码包内置了伪静态规则，然后就可以开始奔放了

### 其他
预计下个版本将会添加跳转(用于QQ/微信防红)，有好的跳转模板可以给我丢个链接，QQ：2457249379
### 功能介绍(时间仓促，写了最基本的功能)
1. 统一规范的API接口
1. 短链接管理(链接时效、链接状态、点击量统计)
1. 用户管理(用户状态、登录注册)