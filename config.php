<?php
/*************************************************************************
 File Name: config.php
 Author: chliny
 mail: chliny11@gmail.com
 Created Time: 2013年03月26日 星期二 21时15分10秒
 ************************************************************************/
$hostname = "localhost//MYSQL DATABASE HOST NAME";
$dbuser = "MYSQL DATABASE USERNAME";
$dbname = "xb";
$mytoken = "微信公众平台token";
$dbpassword = "MYSQL DATABASE PASSWORD";
$rooturl = "http://服务器响应地址";



define("TOKEN", $mytoken);
define("MAXGOODSNUM",10);//用户能保存的物品数目上限
define("ROOTURL",$rooturl);
define("SHOWLEN",0.01);//显示附近物品的范围，单位为经纬度
define("GETLEN",0.001);//用户能获取物品的最远距离
?>
