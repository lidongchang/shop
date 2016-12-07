<?php
define("TOKEN", "weixin");
$wechatObj = new wechatCallbackapiTest();
// $wechatObj->valid();
$wechatObj->responseMsg();

class wechatCallbackapiTest
{
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
        logfile($postStr);
        //extract post data
        if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            <FuncFlag>0</FuncFlag>
                            </xml>";
                $imageTpl= "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Image>
                            <MediaId><![CDATA[%s]]></MediaId>
                            </Image>
                            </xml>";
           $voiceTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[%s]]></MsgType>
<Voice>
<MediaId><![CDATA[%s]]></MediaId>
</Voice>
</xml>";
            $musicTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[%s]]></MsgType>
<Music>
<Title><![CDATA[%s]]></Title>
<Description><![CDATA[%s]]></Description>
<MusicUrl><![CDATA[%s]]></MusicUrl>
<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
</Music>
</xml>";                  
                if($postObj->MsgType == "text"){
                if(!empty( $keyword ))
                {
                    if($keyword == "三国"){
                        $contentStr = "无双";
                    } else if($keyword == "梦幻") {
                        $contentStr = "西游";
                    } else if (strpos($keyword,"成绩") !== false) {
                        //成绩
                        $contentStr = file_get_contents("http://www.lidongchang.top/weixin/score.php?keyword=" . urlencode($keyword));
                    }else if ($keyword = "语音") {
                        $msgType = "voice";
                    // $contentStr = "Welcome to wechat world!";
                    $resultStr = sprintf($voiceTpl, $fromUsername, $toUsername, $time, $msgType, "IfGc4JFjBx-QlHqm5h0V_vdiaK_kEw8A210uoFP0s_4fBMOhhMHNvSoEdB0-xZg7");
                    logfile($resultStr);
                    echo $resultStr;
                    return;
                    }else if ($keyword = "上海滩") {
                        $msgType = "music";
                        $title = "上海滩";
                        $description = "上海滩";
                        $musicUrl = "http://www.lidongchang.top/weixin/jj.mp3";
                        $hqmusicUrl = "http://www.lidongchang.top/weixin/jj.mp3";
                        $thumbMediaId = "IfGc4JFjBx-QlHqm5h0V_vdiaK_kEw8A210uoFP0s_4fBMOhhMHNvSoEdB0-xZg7";
                    $resultStr = sprintf($musicTpl, $fromUsername, $toUsername, $time, $msgType, $title, $description, $musicUrl, $hqmusicUrl, $thumbMediaId);
                    logfile($resultStr);
                    echo $resultStr;
                    return;
                    }
                    $msgType = "text";
                    // $contentStr = "Welcome to wechat world!";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    echo $resultStr;
                }
                else{
                    echo "Input something...";
                }
            }else if($postObj->MsgType == "image"){
                $msgType = "image";
                $contentStr = "你传了一张图片:". $postObj->PicUrl;
                $resultStr = sprintf($imageTpl, $fromUsername, $toUsername, $time, $msgType, "lwvxraqpt3oeMF7BG4VMgmz9AXTJn7Dk17ZprsKe4uSPjlBzzY7tdRevyvnmevcF");
                echo $resultStr;
            }
        }else {
            echo "";
            exit;
        }
    }

    private function checkSignature()
    {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}
function logfile($content) {
    $filename = "log.txt";
    $datatime = date("Y-m-d H:i:s");
    $cnt = $datetime . " " . var_export($content,true) . "\r\n";
    file_put_contents($filename,$cnt,FILE_APPEND);
}
?>
