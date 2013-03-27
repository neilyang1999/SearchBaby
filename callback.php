<?php

class MyCallback
{
    private $postObj;
    private $follower;//订阅公众账号的人
    private $myself;//我自己

    private $mydb;

    function __construct(){
        include_once 'Mysql.php';
        include_once 'config.php';
        $this->mydb = new MyDatabase;
        $this->mydb->connect($hostname,$dbuser,$dbpassword,$dbname);
        $this->mydb->query("set names utf8");
    }

    function __destruct(){
    }

    public function valid()
    {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }
    public function responseMsg($postStr='')
    {
	//get post data, May be due to the different environments
	//$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if(empty($postStr))
            $postStr = file_get_contents('php://input');

        //将接收到的数据写入log方便调试
        include_once 'file.php';
        $fileHandle = new rwFile("receive.log");
        $fileHandle->write($postStr);

      	//extract post data
		if (!empty($postStr)){
            $this->postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->follower = $this->postObj->FromUserName;
            $this->myself = $this->postObj->ToUserName;

            $this->checkUser();

            $msgType = $this->postObj->MsgType;
            if($msgType == "text"){
                $content = trim($this->postObj->Content);
                $keyword = explode(" ",$content);
                if(empty( $keyword )){
                    $this->sendEmpty();
                }else {
                    switch ($keyword[0]){
                        case "help":
                            $this->sendHelp();
                            break;
                        case "status":
                            $this->sendStatus();
                            break;
                        case "show":
                            $this->sendShow();
                            break;
                        case "put":
                            $this->removeGoods(array_slice($keyword,1));
                            break;
                        case "get":
                            $this->getGoods(array_slice($keyword,1));
                            break;
                        case "merge":
                            $this->mergeGoods(array_slice($keyword,1));
                            break;
                        default:
                            $this->otherMsg(); 
                    }
                }
            }else if($msgType == "location"){
                $this->setLocation();
            }else if($msgType == "event"){
                $event = $this->postObj->Event;
                if($event == "subscribe"){
                    $this->checkUser();
                }
            }else{
                $this->sendTypeError();
            }
        }else{
        	echo "";
        	exit;
        }
    }
	private function checkSignature()
	{
        include_once 'config.php';
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
    private function echoText($contentStr){
        $msgType = "text";
        $time = time();
        $textTpl = "
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[%s]]></MsgType>
<Content><![CDATA[%s]]></Content>
<FuncFlag>0</FuncFlag>
</xml>";             
        $textTpl = trim($textTpl);
        $contentStr = trim($contentStr);
       $resultStr = sprintf($textTpl, $this->follower, $this->myself, $time, $msgType, $contentStr);
        echo $resultStr;
    }

    private function sendhelp(){
        $contentStr = "
    寻宝江湖是一款基于LBS的沙盒小游戏，游戏地图即现实地图，当您所在位置对应的虚拟世界存在物品，则您可以通过相应命令获取到.对于您已经拥有的物品，您可以对他们进行拆解，或者合成新的物品。下面是一些您可以使用的命令：
help：查看此帮助；
status：查看您已有的物品，还能携带的物品数，及其他个人资料；
show：查看附近是否有物品可以获取。发送此命令前您需要先上传您的位置；
get [物品名称 物品多称]...：获取物品，多个物品请用空格隔开，该物品必须在您所在位置100米内，不加物品名称则默认为离您最近的物品；
put [物品名称 物品多称]...：放下（扔掉）物品，多个物品请用空格隔开，不加物品名称则默认为您物品栏中最后一个物品。您放下的任何物品可以被任何一个人捡起；
merge 物品名称 [物品名称]..：将多个物品合成，被合成的物品必需是您已经持有的物品；

    注意：该游戏还在开发中，如果您无帮助开发者测试的意愿，请及时退订。如您有任何想法，欢迎回复告知我。
";
        $this->echoText($contentStr);
    }
    private function sendEmpty(){
    	$contentStr = "欢迎来到寻宝江湖，发送help可查看游戏玩法。
    注意：该游戏还在开发中，如果您无帮助开发者测试的意愿，请及时退订。如您有任何想法，欢迎回复告知我。";
        $this->echoText($contentStr);
    }

    private function checkUser(){
        $userId = $this->follower;
        $query = "select * from users where id='$userId'";
        $result = $this->mydb->query($query);
        $followers = $this->mydb->fetch_array($result);
        if(empty($followers)){
            $query = "insert into users(id) values('$userId')";
            $insertResult = $this->mydb->query($query);
        }
    }

    private function otherMsg(){
        $contentStr = "对不起，您输入的命令我无法理解。";
        $this->echoText($contentStr);
    }

    private function sendTypeError(){
        $contentStr = "目前尚不支持此类型消息。";
        $this->echoText($contentStr);
    }

    private function uncomplete(){
        $contentStr = "该功能尚未开发完成，敬请期待！";
        $this->echoText($contentStr);
    }

    private function sendStatus(){
        $userId = $this->follower;
        $query = "select x,y,label from users where id='$userId'";
        $result = $this->mydb->query($query);
        $location = $this->mydb->fetch_array($result);
        $x = $location['x'];
        $y = $location['y'];
        $label = $location['label'];

        $query = "select goods.name from have join goods on have.goodid=goods.id where have.userid='$userId'";
        $result = $this->mydb->query($query);
        $goods = array();
        while($eachGoods=$this->mydb->fetch_array($result)){
            $goods[] = $eachGoods;
        };

        $goodsNum = count($goods);
        include_once 'config.php';
        $freeNum = MAXGOODSNUM - $goodsNum;

        $contentStr = "您上一次定位是在${label}。\n";

        if(!empty($goods)){
            $contentStr .= "您拥有${goodsNum}单位物品\n";
            foreach ($goods as $value){
                $contentStr .= $value . " ";
            }
        }

        $contentStr .= "您的背包还剩下${freeNum}单位的空间。";
            
        $this->echoText($contentStr);
    }

    private function sendShow(){
        $this->uncomplete();
    }
    
    private function removeGoods($goods){
        $this->uncomplete();
    }

    private function getGoods($goods){
        $this->uncomplete();
    }

    private function setLocation(){
        $userId = $this->follower;
        $location_x = $this->postObj->Location_X;
        $location_y = $this->postObj->Location_Y;
        $scale = $this->postObj->Scale;
        $label = $this->postObj->Label;
        $query = "update users set x=$location_x,y=$location_y,label='$label' where id='$userId'";
        $result = $this->mydb->query($query);
    }
    
    private function mergeGoods($goods){
        $this->uncomplete();
    }
    
}
?>
