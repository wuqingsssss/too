<?php
final class Common {
	protected $registry;
	public $error;
	public function __construct($registry) {
		$this->registry = $registry;
	}
	public function __get($key) {
		return $this->registry->get ( $key );
	}
	public function __set($key, $value) {
		$this->registry->set ( $key, $value );
	}
	function get_openid() {
		if (! $this->customer->isLogged ()) {
			
			if (! $this->session->data ['platform']) {
				// 微信登录/
				if ((strstr ( $_SERVER ['HTTP_USER_AGENT'], 'MicroMessenger' ) == true) && ($_SERVER ['HTTP_HOST'] == 'www.qingniancaijun.com.cn')) {
					$appid = $this->config->get ( 'wxpay_appid' );
					$appsecret = $this->config->get ( 'wxpay_appsecret' );
					$this->load->service ( 'weixin/interface' );
					if (! isset ( $_GET ['code'] )) {
						$redric_url = rawurlencode ( 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'] );
						$header_url = $this->service_weixin_interface->wget_weixin_access_link ( $appid, $redric_url, 'state', 1, 'code' );
						$this->log_sys->info ( '$redric_url' . $redric_url );
						$this->log_sys->info ( '$header_url' . $header_url );
						Header ( "Location: $header_url" );
						exit ();
					} else {
						$code = $_GET ['code'];
						$this->log_sys->info ( $_GET );
						$openid_array = $this->service_weixin_interface->wget_weixin_access_token ( $appid, $appsecret, $code );
						$this->log_sys->info ( 'back;' . 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'] );
						if (isset ( $openid_array ['openid'] )) {
							$openid = $openid_array ['openid'];
							$platform = 'wechat';
							$device ['openid'] = $openid;
							$device ['platform_code'] = 'wechat';
							$this->session->data ['platform'] = $device;
						}
					}
				} elseif (isset ( $this->request->request ['app_device'] ) && $this->request->request ['app_device'] || $this->request->cookie ['app_device']) { 
					// 手机app端
				    // {"openid":"openidaaaaaa","platform_code":"app"}
					$this->log_sys->info ( 'app_device::this->request::' . $this->request->request ['app_device'] );
					if (! $this->request->cookie ['app_device'] || $this->request->request ['app_device'])
						$this->request->cookie ['app_device'] = $this->request->request ['app_device'];
						
						// print_r($this->request->cookie['app_device']);
					$device = json_decode ( htmlspecialchars_decode ( $this->request->cookie ['app_device'] ), 1 );
					$this->log_sys->info ('app_device::cookie::'. $this->request->cookie ['app_device'] );
					if (isset ( $device ['openid'] ) && isset ( $device ['platform_code'] ))
						$this->session->data ['platform'] = $device;
					else
						unset ( $this->session->data ['platform'] );
				}
			}
			if (isset ( $this->session->data ['platform'] )) {
				$platform = $this->session->data ['platform'];
				// 第三方平台自动登录处理
				
				$openinfo = $this->customer->getPlatForm ( $platform ['openid'], $platform ['platform_code'] );
				
				if($platform['localhref'])$platform['local_host']=dirname($platform['localhref']).'/';

				$this->platform=$platform;

				if ($openinfo && $openinfo ['login_allow'] && $openinfo ['customer_id']) {
					
					$this->customer->setCustomerById ($openinfo ['customer_id'] );
					$this->customer->updateLoginTime ($openinfo ['customer_id']);
				}
				$this->log_sys->info ('get_openid::'.serialize($this->session->data ['platform']));
			}
			//
		}
		
	}
	
	/**
	 * 发送微信模板消息
	 *
	 * @param unknown $openid
	 *        	用户OPENID
	 * @param unknown $template_id
	 *        	模板ID
	 * @param unknown $newurl
	 *        	链接
	 * @param unknown $msg_data
	 *        	消息
	 * @return mixed
	 */
	public function send_msg_by_weixin($openid, $template_id, $newurl, $msg_data) {
		$this->log_sys->info( 'IlexDebug:: send_msg_by_weixin' );
		
		$appid = $this->config->get ( 'wxpay_appid' );
		$appsecret = $this->config->get ( 'wxpay_appsecret' );
		
		if (isset ( $this->session->data ['access_token'] ) && $this->session->data ['access_token']) {
			$token = $this->session->data ['weixin_access_token'];
		} else {
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $appsecret . "";
			$token_tmp = file_get_contents ( $url );
			$token_tmp_array = json_decode ( $token_tmp, true );
			$token = $token_tmp_array ['access_token'];
			$this->session->data ['weixin_access_token'] = $token;
		}
		// $token='OezXcEiiBSKSxW0eoylIeIXYm7jk65RjGSNrFOWtuiyCLcw8vojWXxEOFWkHddDgEE1Z-25xoS00ROXqbkov1S92bGpVExGQu-t3fyT5E7PKzceTHemZ3Qx3a4nS6EOtzViOZ5ku1wUNyTZp1ud8QA';
		$template_array = array (
				'touser' => $openid,
				'template_id' => $template_id,
				'url' => $newurl,
				'topcolor' => '#FF0000',
				'data' => $msg_data 
		);
		
		$msgurl = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$token}";
		$template_array = json_encode ( $template_array );
		// $output=Http::getSSCPOST($msgurl,$template_array,true);
		$output = Http::getPOST ( $msgurl, $template_array );
		
		/*
		 * $template_array = json_encode($template_array);
		 * $ch = curl_init();
		 * curl_setopt($ch, CURLOPT_URL, $msgurl);
		 * curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		 * curl_setopt($ch, CURLOPT_POST, 1);
		 * curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		 * curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		 * curl_setopt($ch, CURLOPT_POSTFIELDS, $template_array);
		 * curl_exec($ch);
		 *
		 *
		 * $output ='';
		 * //var_dump($output);
		 * curl_close($ch);
		 */
		
		$this->log_sys->info ( 'send_msg_by_weixin ::' . serialize($template_array));
		$this->log_sys->info ( 'send_msg_by_weixin ::output' .serialize( $output) );
		return $output;
	}
	
	// 每日起点数随机，而后自增
	public function genOrderSN($prefix = '') {
		// mt_srand((double) microtime() * 1000000);
		// return date('ymd') . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
		$datePart = date ( 'ymd' );
		
		if(defined('ORDER_PREFIX')&&ORDER_PREFIX&&empty ($prefix))
			$prefix=ORDER_PREFIX;
		
		$prefix = $prefix . $datePart;
		$seqLen = 5;
		$len = strlen ( $prefix ) + $seqLen;
		$query = $this->db->query ( "select max(order_id) maxOrderId from ts_order o where o.order_id like '{$prefix}%' and CHAR_LENGTH(o.order_id)={$len} ", false );
		if ($query->num_rows > 0 && ! is_null ( $query->row ['maxOrderId'] )) {
			$maxOrderId = $query->row ['maxOrderId'];
			$maxNum = ( int ) substr ( $maxOrderId, strlen ( $prefix ), $seqLen );
			$maxNum += 1;
		} else {
			@$maxNum = mt_rand ( 500, 900 );
		}
		$seqPart = sprintf ( "%0{$seqLen}d", $maxNum );
		
		//$GLOBALS['_LOGDATA']['order_id']=$prefix . $seqPart;
		$this->log_order->data['order_id']=$prefix . $seqPart;

		
		return $prefix . $seqPart;
	}
	
	// used to format order id for display and reget true order id for php
	public function setOrderId($order_id, $order_add_date = '') {
		if ($order_add_date != '')
			$order_add_date = time ();
		$str = date ( 'Ymdh', strtotime ( $order_add_date ) ) . strval ( $order_id );
		return $str;
	}
	public function getOrderId($order_id_display) {
		$str = ( int ) substr ( $order_id_display, 10, strlen ( $order_id_display ) );
	}
	public function getCustomerName($customer_id) {
		$customer_query = $this->db->query ( "SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id= '" . $customer_id . "'" );
		if ($customer_query->num_rows) {
			return $customer_query->row ['username'];
		} else {
			return '';
		}
	}
	
	/**
	 * 根据电话获取OPENID
	 *
	 * @param unknown $mobile        	
	 */
	public function findOpenId($mobile) {
		$sql = "SELECT tso.openid FROM ts_customer tc, ts_openid_info tso WHERE tc.mobile = '{$mobile}' AND tso.customer_id = tc.customer_id";
		
		$query = $this->db->query ( $sql, false );
		
		return $query->row ['openid'];
	}
	
	/**
	 * 根据用户ID获取OPENID
	 *
	 * @param unknown $mobile        	
	 */
	public function findOpenIdwithCustomerID($customer_id) {
		$sql = "SELECT openid FROM ts_openid_info  WHERE customer_id = '{$customer_id}'";
		
		$query = $this->db->query ( $sql, false );
		
		return $query->row ['openid'];
	}
}

?>