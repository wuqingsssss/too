<?php

function error_handler($errno, $errstr, $errfile, $errline) {
	global $log, $config,$log_sys;
	switch ($errno) {
		case E_NOTICE:
		case E_USER_NOTICE:
			$error = 'Notice';
			if(defined('DEBUG')&&DEBUG){
				//$log->error('PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
				$log_sys->trace('PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
			}
			break;
		case E_WARNING:
		case E_USER_WARNING:
			$error = 'Warning';
			if(defined('DEBUG')&&DEBUG){
				//$log->error('PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
				$log_sys->debug('PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
			}
			break;
		case E_ERROR:
		case E_USER_ERROR:
			$error = 'Fatal Error';
			 //$log->error('PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
			 $log_sys->error('PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
			 sendAlertMailMsg($error,'PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
			break;
		default:
			$error = 'Unknown';
			if(defined('DEBUG')&&DEBUG){
			//$log->error('PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);

			$log_sys->warn('PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
	}
			break;
	}

	if ($config->get('config_error_display')) {
		echo $config->get('config_error_display').'<b>error_handler：' . $error . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
	}

	return true;
}
 function sendAlertMailMsg($level,$text){
 	global $log, $config,$log_sys,$emails_waitting;
	$subject = $level.'::'.HTTP_SERVER.'::'.html_entity_decode($config->get('config_name'), ENT_QUOTES, 'UTF-8');
	// Text
	if(!$config->get('config_smtp_host')){ 
		$emails_waitting[]=array('level'=>$level,'text'=>$text);
		return;
	}

	$mail = new Mail();
	$mail->protocol = $config->get('config_mail_protocol');
	$mail->parameter = $config->get('config_mail_parameter');
	$mail->hostname = $config->get('config_smtp_host');
	$mail->username = $config->get('config_smtp_username');
	$mail->password = $config->get('config_smtp_password');
	$mail->port = $config->get('config_smtp_port');
	$mail->timeout = $config->get('config_smtp_timeout');
	$mail->setTo($config->get('config_email'));
	$mail->setFrom($config->get('config_email'));
	$mail->setSender('error_handler');
	$mail->setSubject($subject);
	$mail->setText($text);
	$mail->send();

	// Send to additional alert emails
	
	$email=$config->get('config_alert_emails');
	//if(!$email)$email=ERROR_NOTICE_MAIL;
	$emails = explode(',', $email);

	foreach ($emails as $email) {
		if ($email && preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i', $email)) {
			$mail->setTo($email);
			$mail->send();
		}
	}

}
function is_env_development(){
	if(defined('ENVIRONMENT') && ENVIRONMENT==='development'){
		return TRUE;	
	}
	
	return FALSE;
}

function resizeThumbImage($image,$width=0,$height=0,$no_image=false){
	$registrty=getRegistry();
	
	$registrty->get('load')->model('tool/image');
	
	if($image && file_exists(DIR_IMAGE.$image)){
		$origin_image=$image;
	}else if($no_image){
		$origin_image='no_image.jpg';
	}else{
		$origin_image=false;
	}
	
	if($origin_image && file_exists(DIR_IMAGE.$origin_image)){
		if($width||$height){
			$thumb=$registrty->get('model_tool_image')->resize($origin_image, $width, $height);
		}else{
			$thumb=HTTP_IMAGE.$origin_image;
		}
  	}else{
  		$thumb=false;
  	}
  	
  	return $thumb;
}

function changeProductResults($results,$obj,$promotion='',$image_width='',$image_height=''){
	$products=array();
	
	if(!$image_width){
		$image_width=$obj->config->get('config_image_product_width');
	}
	
	if(!$image_height){
		$image_height=$obj->config->get('config_image_product_height');
	}
	
	if(isset($obj->request->get['path'])){
		$url='path='.$obj->request->get['path'];
	}else{
		$url='';
	}
	
	if($promotion){
		$url_after='&p_code='.$promotion;
	}else{
		$url_after='';
	}

	foreach ($results as $result) {

			$image=resizeThumbImage($result['image'],$image_width, $image_height,true);

			if (($obj->config->get('config_customer_price') && $obj->customer->isLogged()) || !$obj->config->get('config_customer_price')) {
				$price = $obj->currency->format($obj->tax->calculate($result['price'], $result['tax_class_id'], $obj->config->get('config_tax')));
			} else {
				$price = false;
			}
			
			/*if ((float)$result['special']) {
				$special = $obj->currency->format($obj->tax->calculate($result['special'], $result['tax_class_id'], $obj->config->get('config_tax')));
			} else {
				$special = false;
			}	*/
			
			if ($obj->config->get('config_tax')) {
				$tax = $obj->currency->format((float)$result['special'] ? $result['special'] : $result['price']);
			} else {
				$tax = false;
			}				
			
			if ($obj->config->get('config_review_status')) {
				$rating = (int)$result['rating'];
			} else {
				$rating = false;
			}
			
			//计算商品特殊价格
			if($promotion){
				if($promotion==EnumPromotionTypes::REGISTER_DONATION){
					$special=$obj->currency->format(0);
				}else if($promotion==EnumPromotionTypes::TOTAL_DONATION){
					$special=$obj->currency->format(0);
				}
			}
                        if(isset($result['promotion']['promotion_price'])){
			    $result['promotion']['promotion_price']= $obj->currency->format($result['promotion']['promotion_price']);
			}
			
			

			$products[] = array(
				'product_id'  => $result['product_id'],
				'thumb'       => $image,
				'name'        => $result['name'],
				'sku'         => $result['sku'],
				'icons'       => $result['icons'],
				'subtitle'    => $result['subtitle'],
				'unit'        => $result['unit'],
				'origin'      => $result['origin'],
				'garnish'     => isset($result['garnish'])?$result['garnish']:"",
				'cooking_time'=> isset($result['cooking_time'])?$result['cooking_time']:"",
				'calorie'     => isset($result['calorie'])?$result['calorie']:"",
				'period_id'   => isset($result['period_id'])?$result['period_id']:"",
				'follow'      => isset($result['follow'])?$result['follow']:"",
				'description' => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
				'price'       => $price,
			    'promotion'   => $result['promotion'],
				'promotions'   => $result['promotions'],
				'tax'         => $tax,
				'available'   =>  $result['available'],
				'status_name'   =>  $result['status_name'],
				'rating'      => $result['rating'],
				'manufacturer_id'=> $result['manufacturer_id'],
				'reviews'     => (int)$result['reviews'],
				'href'        =>$result['link_url']?$result['link_url']: $obj->url->link('product/product', $url.'&product_id=' . $result['product_id'].$url_after)
			);
		}
		return $products;
}

function get_option_value($option_value_id){
	$config=getConfig();
	$db=getRegistry()->get('db');
	
	$sql="SELECT name FROM ".DB_PREFIX."option_value_description WHERE option_value_id=".(int)$option_value_id." AND language_id=".(int)$config->get('config_language_id');

	$query = $db->query($sql);
	
	if($query->num_rows){
		return $query->row['name'];
	}else{
		return '';
	}
}

function getCategoryManufacturers($category_id){
	$registry=getRegistry();
	
	$registry->get('load')->model('catalog/category');
	$registry->get('load')->model('catalog/manufacturer');
	$url=$registry->get('url');
	
	
	$results=$registry->get('model_catalog_category')->getCategoryManufacturers($category_id);

	$manufacturers=array();
	
	foreach($results as $result){
		$result_info=$registry->get('model_catalog_manufacturer')->getManufacturer($result['manufacturer_id']);
		
		if($result_info){
			$manufacturers[]=array(
				'name' => $result_info['name'],
				'thumb' => resizeThumbImage($result_info['image'],0,0,TRUE),
				'href' => $url->link('product/manufacturer/product&manufacturer_id='.(int)$result_info['manufacturer_id'])
			);
		}
	}
	
	return $manufacturers;
}

function show_product_price($result){
	$html='';
	
	$currency=getRegistry()->get('currency');
	$language=getRegistry()->get('language');
	
	if($result['special']) {
		echo '<p class="price">'.$currency->format($result['special']).'<span>'.$currency->format($result['price']).'</span></p>';
	}else{
		if($result['price'] > 0) {
			echo '<p class="price">'.$currency->format($result['price']).'<span></span></p>';
		}else{
			echo '<p class="price">'.$language->get('text_enquiry_price').'<span></span></p>';
		}
	}
	
}
/*
 * 检测是否存在使用满足促销规则的商品
 * */
function checkPromotionProduct($product_id,$promotion_code){
	$config=getConfig();
	$db=getRegistry()->get('db');
	
	$sql="SELECT product_id FROM  ".DB_PREFIX."p_rule pr LEFT JOIN  ".DB_PREFIX
			."pr_to_product prp ON (pr.pr_id=prp.pr_id) WHERE LCASE(pr.pr_code)='".$db->escape(strtolower($promotion_code))."' AND prp.product_id=".(int)$product_id;
			
	$query=$db->query($sql);
	
	if($query->rows){
		return TRUE;
	}else{
		return FALSE;
	}	
}

function getPromotionProductPrice($product_id,$promotion_code){
	if($promotion_code==EnumPromotionTypes::REGISTER_DONATION){
		return 0;
	}else if($promotion_code==EnumPromotionTypes::TOTAL_DONATION){
		return 0;
	}else if($promotion_code==EnumPromotionTypes::ZERO_BUY){
		return 0;
	}
}

function isFirstBuy(){
	$config=getConfig();
	$load=getRegistry()->get('load');
	
	$load->model('account/order');
	
	$filter=array(
			'filter_not_order_status_ids' => array(getConfig()->get('config_order_cancel_status_id'),getConfig()->get('config_order_nopay_status_id'))
	);
		
	$order_total=getRegistry()->get('model_account_order')->getTotalOrders($filter);
	
	if($order_total > 0){
		return FALSE;
	}else{
		return TRUE;
	}
}


function getProductIcons($product_id){
	$sql="SELECT pr.pr_code FROM ".DB_PREFIX."p_rule pr LEFT JOIN  ".DB_PREFIX
			."pr_to_product prp ON (pr.pr_id=prp.pr_id) WHERE prp.product_id=".(int)$product_id." GROUP BY pr.pr_id";

	$config=getConfig();
	$db=getRegistry()->get('db');	
	
	$query=$db->query($sql);
	
	$results=array();
	
	foreach($query->rows as $row){
		$results[]=$row['pr_code'];
	}
	
	return $results;
}


	/**
	 * 面包屑
	 */
	function createBreadcrumbs($text,$href,$separator)
	{
		return array(
       		'text'      =>$text,
			'href'      => $href,
      		'separator' => $separator
		);
	}

	 function createActions($text,$href)
	{
		return  array(
				'text' => $text,
				'href' => $href
		);
	}

	/**
	 *
	 * 获取get请求中的参数值，没有指定参数返回默认值
	 * @param unknown_type $param   ：   参数名
	 * @param unknown_type $isCover : 是否强制转换
	 * @param unknown_type $type    : 转换类型
	 * @param unknown_type $default : 默认值
	 */
	 function getParamValue($param,$isCover=false,$type='int',$default)
	{
		$value = null;
		$request = getRegistry()->get('request');
		if(isset($request->get[''.$param])){
			if($isCover)
			{
				if($type=='int')
				{
					$value=(int)$request->get[''.$param];
				}
				else{
					$value=$request->get[''.$param];
				}
			}
			else {
				$value=$request->get[''.$param];
			}
		}else{
			$value=$default;
		}
		return $value;
	}

	 function getPageObj($total,$page,$limit,$link,$param)
	{
		$language = getRegistry()->get('language');
		$url = getRegistry()->get('url');
		
		$pagination = new Pagination();
		$pagination->total = $total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->text = $language->get('text_pagination');
		$pagination->url = $url->link($link, $param, 'SSL');
		return $pagination;
	}


	 function initOperStatus()
     {
         $error = getRegistry()->get('error');
         $data = getRegistry()->get('data');
         $session = getRegistry()->get('session');

         if (isset($error['warning'])) {
             $data['error_warning'] = $error['warning'];
         } else {
             $data['error_warning'] = '';
         }

         if (isset($session->data['success'])) {
             $data['success'] = $session->data['success'];
             unset($session->data['success']);
         } else {
             $data['success'] = '';
         }
     }


    function mobile_check($mobile){
        if($mobile){
            return true;
    //                return preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/",$mobile);
        }

        return false;
    }


function getServerName()
{
	$ServerName = strtolower($_SERVER['SERVER_NAME']?$_SERVER['SERVER_NAME']:$_SERVER['HTTP_HOST']);

	if( strpos($ServerName,'http://') )
	{
		return str_replace('http://','',$ServerName);
	}

	$ServerPort='';

	if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80'){
		$ServerPort=':'.$_SERVER['SERVER_PORT'];
	}

	return 'http://'.$ServerName.$ServerPort.$_SERVER['REQUEST_URI'];
}


function is_current_url($url){
	$current=urldecode(getServerName());

	if(str_replace(HTTP_SERVER,'',$url) == str_replace(HTTP_SERVER,'',$current)){
		return true;
	}else{
		return false;
	}
}


class EnumPartners{

	const FIVE_EIGHT = "58";
	const PING_BAN = "andriod";
	const APP_DOUGUO = "douguo";
	const PING_BEIQI = "beiqi";
	const JD_PDJ = "jd";
	const MT_WM = "meituan";
	const BD_WM = "baidu";
	const APP_ELE = "ele";
	const MT_TG = "meituantg";
	const NM_TG = "nuomitg";
	const DP_TG = "dianpingtg";
	const APP_SH = "shihuiapp";
	const QNCJ_MARKET = "qncgmarket";
	
	
	static function  getAllPartners(){
		return array(
			EnumPartners::QNCJ_MARKET =>"菜君市场部",
			EnumPartners::APP_DOUGUO =>"豆果",
			EnumPartners::JD_PDJ => "京东到家",
			EnumPartners::MT_WM => "美团外卖",
			EnumPartners::MT_TG => "美团团购",
			EnumPartners::BD_WM => "百度外卖",
			EnumPartners::NM_TG => "糯米团购",
			EnumPartners::DP_TG => "点评团购",
			EnumPartners::APP_SH => "实惠",
			EnumPartners::APP_ELE => "饿了么",
			EnumPartners::PING_BEIQI => "北汽",
			EnumPartners::FIVE_EIGHT => "58",
			EnumPartners::PING_BAN => "冷柜",
		);
		//return "ddd";
	}

	static function getPartnerInfo($code){
		if($code==EnumPartners::APP_DOUGUO){
			return "豆果";
		}
	    elseif($code==EnumPartners::QNCJ_MARKET){
			return "菜君市场部";
		}elseif($code==EnumPartners::JD_PDJ){
			return "京东到家";
		}
	    elseif($code==EnumPartners::MT_WM){
			return "美团外卖";
		}
	    elseif($code==EnumPartners::BD_WM){
			return "百度外卖";
		} elseif($code==EnumPartners::APP_ELE){
			return "饿了么";
		}elseif($code==EnumPartners::MT_TG){
			return "美团团购";
		}elseif($code==EnumPartners::NM_TG){
			return "糯米团购";
		}elseif($code==EnumPartners::DP_TG){
			return "点评团购";
		}elseif($code==EnumPartners::APP_SH){
			return "实惠";
		}elseif($code==EnumPartners::FIVE_EIGHT){
			return "58到家";
		}elseif($code==EnumPartners::PING_BEIQI){
			return "北汽";
		}elseif($code==EnumPartners::PING_BAN){
			return "冷柜";
		}else{
			return "站内";
		}
	}
}
class EnumDelivery{

	const MSS = "meishisong";
	const QNCJ = "qncj";
	const IMDADA = "imdada";
	const JDZB = "jdzb";

	static function  getAllDelivery(){
		return array(
				EnumDelivery::QNCJ => "菜君自营",
				EnumDelivery::MSS =>"美食送",
				EnumDelivery::JDZB =>"京东众包",
				EnumDelivery::IMDADA =>"达达",
		);

		//return "ddd";
	}

	static function getDeliveryInfo($code){

		if($code==EnumDelivery::MSS){
			return "美食送";
		}elseif($code==EnumDelivery::QNCJ){
			return "菜君自营";
		}elseif($code==EnumDelivery::IMDADA){
			return "达达";
		}elseif($code==EnumDelivery::JDZB){
			return "京东众包";
		}else{
			return false;
		}

	}

}

function getShowLimit(){
	return 12;
}

	 
