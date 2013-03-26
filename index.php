<?php
/**
  * wechat php test
  */
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
            }else{
                $this->sendErrorType();
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
        $contentStr = "寻宝江湖是一款基于LBS的沙盒小游戏，游戏地图即现实地图，";
        $this->echoText($contentStr);
    }
    private function sendEmpty(){
    	$contentStr = "欢迎来到寻宝江湖，发送help可查看游戏玩法。";
        $this->echoText($contentStr);
    }
}
?>
