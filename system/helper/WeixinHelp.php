<?php

/**
 * 微信接口开发接口类
 * ============================================================================
 * $Author: litao $
 * $Id: WeixinHelp.php 15013 2008-10-23 09:31:42Z testyang $
*/
final class WeixinHelp {
	
	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->customer = $registry->get('customer');
		$this->session = $registry->get('session');
		$this->load = $registry->get('load');
	}
	
/*
 * 获取操作链接token
 * */
public function get_weixin_access_token($appid,$appsecret,$reset=false)
{
  if(isset($this->session->data['access_token_'.$appid])&&$this->session->data['access_token_'.$appid]!=''&&!$reset){
     $token= $this->session->data['access_token_'.$appid];
   }
  else
  {
     $tokeninfo= $this->hget_weixin_access_token($appid,$appsecret);
     if($tokeninfo)
     { 
	 $this->session->data['access_token_'.$appid]=$tokeninfo['access_token'];
	 $token=$tokeninfo['access_token'];
	 }
      else
     {
      unset($this->session->data['access_token_'.$appid]);
	  $token=false;
     }
   }
return $token ;
}
/*
 * 获取后台服务器授权
 * 
 * */
public function hget_weixin_access_token($appid,$appsecret)
{
$url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret."";
$res=$this->hget_json_data($url);

if($res['errmsg'])
{
return false;
}
else
{   
return $res;
}
}
/*
 * 获取开放平台网页登录授权链接
 *
 * */
public function wget_weixin_access_link($appid,$redirect_uri,$state='state',$type=1,$response_type='code')
{
	if($type==1){//'snsapi_base'
$url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=$response_type&scope=snsapi_base&state=$state#wechat_redirect";
	}
	elseif($type==2) 
	{//snsapi_userinfo
$url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=$response_type&scope=snsapi_userinfo&state=$state#wechat_redirect
	";	
	}else{	
  $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=$response_type&scope=snsapi_base&state=$state#wechat_redirect";	
	}
return url;
}
/*
 * 获取开放平台网页登录授权token
 * 
 * */
public function wget_weixin_access_token($appid,$appsecret,$code)
{
$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=$code&grant_type=authorization_code";
$res=$this->hget_json_data($url);

if($res['errmsg'])
{
return false;
}
else
{   
return $res;
}
}

/*
 * 获取所有微信关注信息
 * */
public function hget_all_weixin_users($token,$next_openid='')
{
	$url="https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$token."&next_openid=$next_openid";
	$res=$this->hget_json_data($url);
	return $res;
}
/*
 * 根据用户openid和token获取用户基本信息，web端
 *
 * */
public function wget_weixin_userinfo($openid,$token)
{
$url="https://api.weixin.qq.com/sns/userinfo?access_token=".$token."&openid=$openid&lang=zh_CN";//omLQFj3gs0A76K_vx79oKRTLzPM8
$res=$this->hget_json_data($url);
if($res['errcode'])
{
return false;
}
else
{   
return $res;
}
}

/*
 * 根据web端token获取网页授权ticket
 *
 * */
public function hget_jsapi_ticket($token)
{
$url="https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=$token&type=jsapi";
$res=$this->hget_json_data($url);

if($res['errcode'])
{
return false;
}
else
{   
return $res;
}


}

/*
 * 根据用户openid和token获取用户基本信息，服务器端
 *
 * */
public function hget_weixin_userinfo($openid,$token)
{
$url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$token."&openid=$openid&lang=zh_CN";//omLQFj3gs0A76K_vx79oKRTLzPM8
$res=$this->hget_json_data($url);
if($res['errcode'])
{
return false;
}
else
{   
return $res;
}
}
/*
 * 服务器端处理微信用户信息
 * */
public function set_weixin_users($userinfo)
{
		
}


/*
 * 远程请求提交方法GET
 * */

public  function hget_json_data($url,$type=1)
{

 $this->load->library('json');
	
 $res_str=file_get_contents($url);

 $res=Json::decode($res_str,$type);

return $res;
}
/*
 * 远程请求提交方法POST
 * */
public  function hpost_json_data($url, $post = null,$type=1) 
{ 
	$this->load->library('json');
    if (is_array($post)) {
           ksort($post);
           $content = http_build_query($post);
           $content_length = strlen($content);
      
    }
    else
	{
	    $content = $post;
        $content_length = strlen($post);
	};
		
	 $options = array(
            'http' => array(
                'method' => 'POST',
                'header' =>
                "Content-type: application/x-www-form-urlencoded\r\n" .
                "Content-length: $content_length\r\n",
                'content' => $content
            ) );
	$res_str=file_get_contents($url, false, stream_context_create($options));
	
	$res=Json::decode($res_str,$type);
return $res;	
}


/**远程获取客服列表
@param   string   $token
@param   integer  $online 0全部 1在线

@return  $arr
*/
public function hget_weixin_kefu_list($token,$online=0)
{
	if($online){
		$url="https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist?access_token=".$token;
	}else
	{
		$url="https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token=".$token;
	}
	$res=$this->hget_json_data($url);
	if($res['errcode'])
	{
		return false;
	}
	else
	{
		foreach($res['kf_online_list'] as $key=>$user)
			$arr[$user['kf_account']]=$user;

		return $arr;
	}
}



/* 本地获取微信关联用户信息*/
public function get_weixin_userinfo($fromUsername)
{
	return ;
}

/* 通过服务器获取客服列表*/
public function get_wx_kefu_list($areaname,$type=1)
{

	return ;
}



/* 绑定微信客户基本信息到网站数据库*/
public function insert_weixin_users($w)
{

return $userid;
}

/* 微信客户快速下单*/
public function insert_weixin_order($user_id,$goods_id,$agency_id)
{

	return $result;
}

/*判断微信code识别码并绑定商城账号
 $hash  身份验证识别码
 $w     微信类型对象
 返回news数组*/
public function validate_weixin_code($hash,$w)
{
	if ($hash)
	{
		$sql = "SELECT user_id FROM ". $GLOBALS['ecs']->table('weixin_users')." WHERE weixin_username = '".$w->fromusername."'";
		$count = $GLOBALS['db']->getRow($sql);
		if(!$count){
			include_once(ROOT_PATH . 'includes/lib_passport.php');
			$id = register_hash('decode', $hash);
			if ($id > 0)
			{
				$sql = "UPDATE " . $GLOBALS['ecs']->table('weixin_users') . " SET user_id = '$id' WHERE weixin_username='".$w->fromusername."'";
				$GLOBALS['db']->query($sql);

				return true;
			}
		}
	}
	return false;
	 
}
/* 通过hash 解除绑定接口*/
public function release_weixin_code($hash,$w)
{
	if ($hash)
	{
		include_once(ROOT_PATH . 'includes/lib_passport.php');
		$id = register_hash('decode', $hash);
		if ($id > 0)
		{
			/* 数据库操作*/
			
				return true;
		}
			
	}
	return false;
}
 

/* 根据经纬度调取自提点*/
public function get_point_by_location($x,$y)
{

		return ;
}

/**
 *  @desc 根据两点间的经纬度计算距离
 *  @param float $lat 纬度值
 *  @param float $lng 经度值
 */
public function getDistance($lat1, $lng1, $lat2, $lng2)
{
	$earthRadius = 6367000; //approximate radius of earth in meters

	/*
	 Convert these degrees to radians
	 to work with the formula
	 */

	$lat1 = ($lat1 * pi() ) / 180;
	$lng1 = ($lng1 * pi() ) / 180;

	$lat2 = ($lat2 * pi() ) / 180;
	$lng2 = ($lng2 * pi() ) / 180;

	/*
	 Using the
	 Haversine formula

	 http://en.wikipedia.org/wiki/Haversine_formula

	 calculate the distance
	 */

	$calcLongitude = $lng2 - $lng1;
	$calcLatitude = $lat2 - $lat1;
	$stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
	$calculatedDistance = $earthRadius * $stepTwo;

	return round($calculatedDistance);
}



public function checkSignature()
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




}


/* 微信消息接口操作类*/
class cls_weixin_msg
{

	var $msgtype              ='';
	var $postStr              ='';
	var $fromusername         = '';
	var $tousername           = '';
	var $keyword              = '';
	var $time0                 = 0;
	var $flag                 = 0;

	var $msgcount             = 1;
	var $texttpl              = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[text]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>%d</FuncFlag>
							</xml>";
	var $newstpl               ="<xml>
 <ToUserName><![CDATA[%s]]></ToUserName>
 <FromUserName><![CDATA[%s]]></FromUserName>
 <CreateTime>%s</CreateTime>
 <MsgType><![CDATA[news]]></MsgType>
 <ArticleCount>%s</ArticleCount>
 <Articles>%s</Articles>
 <FuncFlag>%s</FuncFlag>
 </xml>";

	var $locationtpl               ="<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[location]]></MsgType>
<Location_X>%s</Location_X>
<Location_Y>%s</Location_Y>
<Scale>20</Scale>
<Label><![CDATA[%s]]></Label>
<FuncFlag>%s</FuncFlag>
</xml>
";

	var $customer_servicetpl0      ="
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[transfer_customer_service]]></MsgType>
</xml>";

	var $customer_servicetpl1      ="
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[transfer_customer_service]]></MsgType>
<TransInfo>
   <KfAccount>![CDATA[%s]]</KfAccount>
</TransInfo>
</xml>";


		

	function __construct($postStr)
	{
		$this->cls_weixin_msg($postStr);
	}

	function cls_weixin_msg($postStr)
	{

	 if (!empty($postStr))
	 {
	 	$postObj              = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

	 	 
	 	 
	 	$this->fromusername         = $postObj->FromUserName;
	 	$this->tousername           = $postObj->ToUserName;
	 	$this->keyword              = trim($postObj->Content);
	 	$this->msgtype              =trim($postObj->MsgType);
	 	$this->event                =trim($postObj->Event);
	 	$this->eventkey             =trim($postObj->EventKey);
	 	$this->mediaid              =trim($postObj->MediaId);
	 	$this->format               =trim($postObj->Format);
	 	$this->recognition          =trim($postObj->Recognition);
	 	$this->msgId                =trim($postObj->MsgId);
	 	$this->createTime           =trim($postObj->CreateTime);

	 	if($this->msgtype=='location'){
	 		$this->location_x           =trim($postObj->Location_X);
	 		$this->location_y           =trim($postObj->Location_Y);
	 		$this->scale                =trim($postObj->Scale);
	 		$this->label                =trim($postObj->Label);
	 	}

	 	if($this->msgtype=='voice')$this->keyword.=$this->recognition;

	 	$this->time0                =local_strtotime(local_date('Y-m-d H:i:s'));
	 }

	}

	function transmitText($content)
	{


		return sprintf($this->texttpl, $this->fromusername, $this->tousername, $this->time0, $content, $this->flag);
	}

	function transmitNews($news)
	{
		$this->msgcount=count($news);
		$content='';
		foreach($news as $key=>$item)
		{

			$content.=$this->getNewsItem($item);

		}

		$resultStr = sprintf($this->newstpl, $this->fromusername, $this->tousername, $this->time0,$this->msgcount, $content,$this->flag);
		return $resultStr;
	}

	/* function transmitLocation($location,$title)
	 {
	 $xy=explode(',', $location);


	 return sprintf($this->locationtpl, $this->fromusername, $this->tousername, $this->time0, $xy[0],$xy[1],$title, $this->flag);
	 }*/

	function transmitCustomer_service($kefu)
	{
		if(empty($kefu))
			$res=sprintf($this->customer_servicetpl0, $this->fromusername, $this->tousername, $this->time0);
		else
	  $res=sprintf($this->customer_servicetpl1, $this->fromusername, $this->tousername, $this->time0, $kefu);
		return $res;
	}

	function getNewsItem($item)
	{
		$newscontentStr="<item>
 <Title><![CDATA[".$item['title']."]]></Title>
 <Description><![CDATA[".$item['desc']."]]></Description>
 <PicUrl><![CDATA[".$item['src']."]]></PicUrl>
  <Url><![CDATA[".$item['linkurl']."]]></Url>
 </item>";

		 
		return $newscontentStr;
	}

}
?>