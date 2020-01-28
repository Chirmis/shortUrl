<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------


//10进制转62进制
function from10to62($dec) 
{
    $dict = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $result = '';
    do {
        $result = $dict[$dec % 62] . $result;
        $dec = intval($dec / 62);
    } while ($dec != 0);
    return $result;
}

//生成短连接特征值
function getShortUrl($url)
{
    $encode_url = md5($url) . strtoupper(substr(sha1($url), 7, 16));
    $url_arr = str_split($encode_url);
    $num = null;
    //前4位拼接
    for ($i=0; $i < 5; $i++) { 
        $num .= ord($url_arr[$i]);
    }
    //后44位相加
    for ($i=5; $i <count($url_arr) ; $i++) {
        //将每个字符所对应的ASCII码值相加
        $num += ord($url_arr[$i]);
    }
    $num = intval($num);
    return from10to62($num);
}

//生成用户密钥
function getAccessKey($input)
{
    $md5 = md5($input);
    $arr = str_split($md5);
    shuffle($arr);
    $str = implode('', $arr);
    return strtoupper($md5.$str);
}

//生成随机字符串
function getRandStr($num=5)
{
	$str = str_shuffle("1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ");
	return substr($str, 0,$num);
}

//CURL
function curl($url, $method='GET', $params=array(), $getinfo=false)
{
    $ip = empty($params["ip"]) ? rand_ip() : $params["ip"]; 
    $header = array('X-FORWARDED-FOR:'.$ip,'CLIENT-IP:'.$ip);
    if(isset($params["header"])){
        $header = array_merge($header,$params["header"]);
    }
    $user_agent = empty($params["ua"]) ? 0 : $params["ua"] ;
    $ch = curl_init();                                                     
    curl_setopt($ch, CURLOPT_URL, $url);                                   
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    if($params["ref"]){
        curl_setopt($ch, CURLOPT_REFERER, $params["ref"]);
    }             
    curl_setopt($ch, CURLOPT_USERAGENT,$user_agent);                       
    curl_setopt($ch, CURLOPT_NOBODY, false);                               
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                        
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);                       
    curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                       
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);                       
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);                        
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);                           
    curl_setopt($ch, CURLOPT_ENCODING, '');                        
    if($method == 'POST'){
        curl_setopt($ch, CURLOPT_POST, true);               
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params["postData"]);               
    }
    $res = curl_exec($ch);
    if ($getinfo) {
        $data = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);
    }else {
        $data = $res;
    }
    curl_close($ch);                                                       
    return $data;
}

//随机IP
function rand_ip()
{
    $ip_long = array(
        array('607649792', '608174079'),
        array('1038614528', '1039007743'),
        array('1783627776', '1784676351'),
        array('2035023872', '2035154943'),
        array('2078801920', '2079064063'),
        array('-1950089216', '-1948778497'),
        array('-1425539072', '-1425014785'),
        array('-1236271104', '-1235419137'),
        array('-770113536', '-768606209'),
        array('-569376768', '-564133889')
    );
    $rand_key = mt_rand(0, 9);
    $ip = long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
    return $ip;
}