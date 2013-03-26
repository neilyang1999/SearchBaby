<?php
//define your token
define("TOKEN", "chlinyu");
$wechatObj = new myCallback();
$wechatObj->responseMsg();
class myCallback
{
    private $postObj;
    private $fromUsername;
    private $toUsername;
	public function valid()
    {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }
    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
      	//extract post data
		if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $msgType = $postObj->MsgType;
            if($msgType == "text"){
                $keyword = trim($postObj->Content);
                if(empty( $keyword )){
                    $this->sendEmpty();
                }else if($keyword == "help"){
                    $this->sendHelp();
                }else{
                    echo "Input something...";
                }
            }else if($msgType == "location"){
                $this->sendMap();
            }else if($msgType == "event"){
                $event = $postObj->Event;
                if($event == "subscribe"){
                    $this->userInit();
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
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>0</FuncFlag>
                    </xml>";             
       $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
        echo $resultStr;
    }

    private function sendhelp(){
        $contentStr = "
            寻宝江湖是一款基于LBS的沙盒小游戏，游戏地图即现实地图，当您所在位置对应的虚拟世界存在物品，则您可以通过相应命令获取到.对于您已经拥有的物品，您可以对他们进行拆解，或者合成新的物品。下面是一些您可以使用的命令：
            help：查看此帮助；
            status：查看您已有的物品，还能携带的物品数，及其他个人资料；
            show：查看附近是否有物品可以获取。发送此命令前您需要先上传您的位置；
            get 物品名称 [物品多称]...：获取物品，多个物品请用空格隔开，该物品必须在您所在位置100米内；
            put 物品名称 [物品多称]...：放下（扔掉）物品，多个物品请用空格隔开；
            merge 物品名称 [物品名称]..：将多个物品合成，被合成的物品必需是您已经持有的物品；
            
            目前仅完成help命令。
            注意：该游戏还在开发中，如果您无帮助开发者测试的意愿，请及时退订。如您有任何想法，欢迎回复告知我。
            "
        $this->echoText($contentStr);
    }
    private function sendEmpty(){
    	$contentStr = "欢迎来到寻宝江湖，发送help可查看游戏玩法。";
        $this->echoText($contentStr);
    }
}
?>
