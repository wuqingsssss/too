<?php
final class EnumLoginCode {
	const ERROR_ACCOUNT = '01';
	const ERROR_ACTIVE = '02';
	const ERROR_Approved = '03';
	const SUCCESS = '1';
}
final class Customer {
	private $customer_id;
	private $firstname;
	private $lastname;
	private $email;
	private $telephone;
	private $fax;
	private $newsletter;
	private $customer_group_id;
	private $address_id;
	private $payment_method;
	private $balance;
	private $shipping_method;
	private $shipping_point_id;
	private $code;
	private $mobile;
	private $city_id;
	private $cbd_id;
	private $thplatforms;
	private $registry;
	private $invitecode;
	public function __construct($registry) {

		$this->registry=$registry;
		if (isset ( $this->session->data ['customer_id'] )) {
		
               $this->setCustomerById($this->session->data ['customer_id'] );
   
		}
	}
	
	public function __get($key) {
		return $this->registry->get($key);
	}
	
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}
	
	
	
	
	public function getInviteCode(){
		
		return $this->invitecode ;
	}
	public function setInviteCode(){
		$this->invitecode = Http::encodeHash( $this->customer_id ,$this->date_added);
	}
	
	public function decodeInviteCode($invitecode) {
		return  Http::decodeHash($invitecod,$this->date_added);
	}

	public function setCustomerById($customer_id){
		$this->session->data ['customer_id']=$customer_id;
			$customer_query = $this->db->query ( "SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . ( int ) $customer_id . "' AND status = '1'" ,false);			
			if ($customer_query->num_rows) {
				
	             $this->setInviteCode();
				if (false&&($customer_query->row ['cart']) && (is_string ( $customer_query->row ['cart'] )) && ! $this->session->data ['cart']) {
					$cart= unserialize ( $customer_query->row ['cart'] );
					if($cart['expires']>=time()){
					$this->session->data ['cart'] = unserialize ( $customer_query->row ['cart'] );
					$this->log_sys->info('setCustomerById_setcart::'.serialize($this->session->data ['cart']));
					}
				}
					
				if (($customer_query->row ['wishlist']) && (is_string ( $customer_query->row ['wishlist'] ))) {
					if (! isset ( $this->session->data ['wishlist'] )) {
						$this->session->data ['wishlist'] = array ();
					}
				
					$wishlist = unserialize ( $customer_query->row ['wishlist'] );
				
					foreach ( $wishlist as $product_id ) {
						if (! in_array ( $product_id, $this->session->data ['wishlist'] )) {
							$this->session->data ['wishlist'] [] = $product_id;
						}
					}
				}
				
				
				$this->customer_id  = $customer_query->row ['customer_id'];
				$this->firstname    = $customer_query->row ['firstname'];
				$this->lastname     = $customer_query->row ['lastname'];
				$this->email        = $customer_query->row ['email'];
				$this->telephone    = $customer_query->row ['telephone'];
				$this->fax          = $customer_query->row ['fax'];
				$this->mobile       = $customer_query->row ['mobile'];
				$this->newsletter   = $customer_query->row ['newsletter'];
				$this->customer_group_id = $customer_query->row ['customer_group_id'];
		
				$this->address_id       = $customer_query->row ['address_id'];
				$this->payment_method   = $customer_query->row ['payment_method'];
				$this->balance          = $customer_query->row['balance'];
				$this->shipping_method  = $customer_query->row ['shipping_method'];
				$this->shipping_point_id= $customer_query->row ['shipping_point_id'];
		
				$this->code             = $customer_query->row ['code'];
				$this->city_id          = $customer_query->row ['city_id'];
				$this->date_added       = $customer_query->row ['date_added'];
				$this->cbd_id           = $customer_query->row ['cbd_id'];
				$this->thplatforms      = $this->getPlatForms ( $this->customer_id );
				$this->invitecode       = $this->getInviteCode();
				
		
				// $this->db->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape(isset($this->session->data['cart']) ? serialize($this->session->data['cart']) : '') . "', wishlist = '" . $this->db->escape(isset($this->session->data['wishlist']) ? serialize($this->session->data['wishlist']) : '') . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "'");
		
				/*
				 * $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'");
				 *
				 * if (!$query->num_rows) {
				 * $this->db->query("INSERT INTO " . DB_PREFIX . "customer_ip SET customer_id = '" . (int)$this->session->data['customer_id'] . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', date_added = NOW()");
				 * }
				*/
			} else {
				$this->logout ();
			}
		
		
		
	}
	
	public function getPlatForm($openid, $platform_code = 'wechat') {
	if(!$openid||!$platform_code){
				return 0;
			}
		$res = $this->db->query ( "select customer_id,login_allow from " . DB_PREFIX . "openid_info WHERE openid='{$openid}' AND platform_code='{$platform_code}'",false );
		return $res->row;
	}
	public function getPlatForms($customer_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "openid_info WHERE customer_id = '" . $customer_id . "'";
		
		$query = $this->db->query ( $sql ,false);
		
		foreach ( $query as $k => $val ) {
			$plateforms [$k] = $val;
		}
		
		return $plateforms;
	}
	
	
	
	public function existPlatForm($platform_code,$customer_id){
		
			if(!$platform_code){
				return 0;
			}
			$tmp=$this->db->query("select openid from " . DB_PREFIX . "openid_info where customer_id='{$customer_id}' AND platform_code='{$platform_code}'");
			if(!$tmp->row){
				return 0;
			}

			return $tmp->row['openid'];


	}
	public function updatePlatForm($openid,$platform_code,$customer_id){
		if(!$openid||!$platform_code){
			return 0;
		}
			$this->db->query("UPDATE " . DB_PREFIX . "openid_info SET  openid='{$openid}',login_allow=1 WHERE customer_id = '" . (int)$customer_id . "'AND platform_code='{$platform_code}' ");

	}
	public function addPlatForm($openid,$platform_code,$customer_id){
		if(!$openid||!$platform_code){
			return 0;
		}
		$this->db->query("insert into " . DB_PREFIX . "openid_info SET openid='{$openid}',platform_code='{$platform_code}',customer_id='{$customer_id}',date_added = NOW()");
		
		
	}
	public function disablePlatForm($openid,$platform_code,$customer_id){
		if(!$openid||!$platform_code){
			return 0;
		}
		$this->db->query ( "UPDATE  " . DB_PREFIX . "openid_info SET login_allow=0 WHERE openid='{$openid}' AND platform_code='{$platform_code}' AND customer_id='" . $this->getId() . "'" );
		
	}
	public function checkEmailExist($email) {
		$customer_query = $this->db->query ( "SELECT * FROM " . DB_PREFIX . "customer where LOWER(email) = '" . $this->db->escape ( strtolower ( $email ) ) . "' AND status = '1'" ,false);
		if ($customer_query->num_rows)
			return 1;
		else
			return 0;
	}
	private $login_code;
	public function getLoginCode() {
		// 00 帐号密码不正确
		// 01帐户未激活
		// 02帐户等待审核验证中
		// 1 成功登录
		return $this->login_code;
	}
	public function login($email, $password) {
		$sql = "SELECT * FROM " . DB_PREFIX . "customer WHERE (LOWER(email) = '" . $this->db->escape ( strtolower ( $email ) ) . "' OR LOWER(mobile) = '" . $this->db->escape ( strtolower ( $email ) ) . "') AND password = '" . $this->db->escape ( md5 ( $password ) ) . "' AND is_delete=0";
		
		$customer_query = $this->db->query ( $sql ,false);
		
		// if (!$this->config->get('config_customer_approval')) {
		// $customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(strtolower($email)) . "' AND password = '" . $this->db->escape(md5($password)) . "' AND status = '1'");
		// } else {
		// $customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(strtolower($email)) . "' AND password = '" . $this->db->escape(md5($password)) . "' AND status = '1' AND approved = '1'");
		// }
		
		if ($customer_query->num_rows) {
			if ($customer_query->row ['status'] == 0) {
				$this->login_code = EnumLoginCode::ERROR_ACTIVE;
				
				return false;
			}
			
			if ($this->config->get ( 'config_customer_approval' )) {
				if ($customer_query->row ['approved'] == 0) {
					$this->login_code = EnumLoginCode::ERROR_Approved;
					
					return false;
				}
			}
			$this->session->data ['customer_id'] = $customer_query->row ['customer_id'];
	
			$this->setCustomerById($this->session->data ['customer_id']);
            $this->updateLoginTime($this->session->data ['customer_id']);
			//$this->db->query ( "UPDATE " . DB_PREFIX . "customer SET ip = '" . $this->db->escape ( $this->request->server ['REMOTE_ADDR'] ) . "',date_latest_login=NOW() WHERE customer_id = '" . ( int ) $customer_query->row ['customer_id'] . "'" );
			
			if(isset($this->session->data['platform'])){
				$platform=$this->session->data['platform'];
				
				if ( $this->existPlatForm($platform['platform_code'],$this->getId())) {
					$this->updatePlatForm($platform['openid'],$platform['platform_code'],$this->getId());					
				} else {
					$this->addPlatForm($platform['openid'],$platform['platform_code'],$this->getId());					
				}
			}
			
			if(isset($this->session->data['un_sharelink_id'])&&$this->session->data['un_sharelink_id']){
				
				$this->db->query ( "UPDATE " . DB_PREFIX . "sharelink SET customer_id = '" . ( int ) $this->getId() . "' WHERE sharelink_id = '" .(int)$this->session->data['un_sharelink_id']. "'" );
					
				unset($this->session->data['un_sharelink_id']);
			}
			
			return true;
		} else {
			$this->login_code = EnumLoginCode::ERROR_ACCOUNT;
			
			return false;
		}
	}
	
	public function updateLoginTime($customer_id){
		
		
		$this->db->query ( "UPDATE " . DB_PREFIX . "customer SET ip = '" . $this->db->escape ( $this->request->server ['REMOTE_ADDR'] ) . "',date_latest_login=NOW() WHERE customer_id = '" . $customer_id . "'" );
		
	}
	public function setAddress($address_id) {
		$this->db->query ( "UPDATE " . DB_PREFIX . "customer SET address_id = '" . $this->db->escape ( $address_id ) . "' WHERE customer_id = '" . ( int ) $this->getId () . "'" );
		$this->address_id = $address_id;
	}
	private function redirect($url, $status = 302) {
		header ( 'Status: ' . $status );
		header ( 'Location: ' . str_replace ( '&amp;', '&', $url ) );
		exit ();
	}
	public function logout() {

		if(isset($this->session->data['platform'])){//关闭平台自动登录		
			$platform=$this->session->data['platform'];			
		    $this->disablePlatForm($platform['openid'],$platform['platform_code'],$this->getId());	
			}
		
		unset ( $this->session->data ['customer_id'] );
		
		$this->customer_id = '';
		$this->firstname = '';
		$this->lastname = '';
		$this->email = '';
		$this->telephone = '';
		$this->fax = '';
		$this->newsletter = '';
		$this->customer_group_id = '';
		$this->address_id = '';
		$this->mobile = '';
		$this->date_added = '';
		// session_destroy();
	}
	public function isLogged() {
		return $this->customer_id;
	}
	public function getId() {
		return $this->customer_id;
	}
	public function getFirstName() {
		return $this->firstname;
	}
	public function getLastName() {
		return $this->lastname;
	}
	public function getName() {
		return trim($this->firstname . ' ' . $this->lastname);
	}
	public function getEmail() {
		return $this->email;
	}
	public function getDateAdded() {
		return $this->date_added;
	}
	public function getDisplayName() {
		if ($this->firstname) {
			return $this->getName ();
		} else {
			return $this->getMaskedMobile ();
		}
	}
	public function getTelephone() {
		return $this->telephone;
	}
	public function getMobile() {
		return $this->mobile;
	}
	public function getMaskedMobile() {
		if (isset ( $this->mobile )) {
			return substr_replace ( $this->mobile, 'XXXX', 3, 4 );
		} else
			return false;
	}
	public function getFax() {
		return $this->fax;
	}
	public function getNewsletter() {
		return $this->newsletter;
	}
	public function getCustomerGroupId() {
		return $this->customer_group_id;
	}
	public function getAddressId() {
		return $this->address_id;
	}
	public function getBalance() {
		$query = $this->db->query ( "SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . ( int ) $this->customer_id . "'" ,false);
			
		return floatval($query->row ['total']);
	}
	
	public function getBalanceSetting() {
	    return $this->balance;
	}
	

	// 积分
	public function getRewardPoints() {
		$query = $this->db->query ( "SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . ( int ) $this->customer_id . "'" ,false);
		
		return $query->row ['total'];
	}
	public function getShippingPointId() {
		return $this->shipping_point_id;
	}
	public function getShippingMethod() {
		return $this->shipping_method;
	}
	public function getCode() {
		if ($this->code == '') {
			$invite_code = md5 ( uniqid () );
			$this->db->query ( "UPDATE " . DB_PREFIX . "customer SET code = '" . $this->db->escape ( $invite_code ) . "' WHERE customer_id = '" . ( int ) $this->session->data ['customer_id'] . "'" );
			return $invite_code;
		} else {
			return $this->code;
		}
	}
	public function getShippingMethodTitle() {
		if ($this->shipping_method != '') {
			$code = explode ( '.', $this->shipping_method );
			$this->language->load ( 'shipping/' . $code ['0'] );
			return $this->language->get ( 'text_title' );
		} else {
			return '';
		}
	}
	public function setShippingMethod($shipping_method) {
		$this->db->query ( "UPDATE " . DB_PREFIX . "customer SET shipping_method = '" . $shipping_method . "' WHERE customer_id = '" . ( int ) $this->session->data ['customer_id'] . "'" );
	}
	
	public function getPaymentMethod() {
		return $this->payment_method;
	}
	
	public function getPaymentMethodTitle() {
		if ($this->payment_method != '') {
			$this->language->load ( 'payment/' . $this->payment_method );
			return $this->language->get ( 'text_title' );
		} else {
			return '';
		}
	}
	
	/**
	 * 设置用户支付方法
	 * @param unknown $paymentMethod
	 */
	public function setPaymentMethod($paymentMethod, $balance = '1') {
	    $this->payment_method = $paymentMethod;
	    $this->balance        = $balance;
		$this->db->query ( "UPDATE " . DB_PREFIX . "customer 
		                    SET payment_method = '" . $this->db->escape($paymentMethod) . "', balance= '" . $this->db->escape($balance) . "'  
		                    WHERE customer_id = '" . ( int ) $this->session->data ['customer_id'] . "'" );
	}
	
	public function setCustomerLocation($city_id, $cbd_id, $point_id) {
		$this->db->query ( "UPDATE " . DB_PREFIX . "customer SET city_id = '" . ( int ) $city_id . "',cbd_id='" . ( int ) $cbd_id . "',shipping_method='pickupaddr.pickupaddr_" . ( int ) $point_id . "' WHERE customer_id = '" . ( int ) $this->getId () . "'" );
	}
	public function getLocationCity() {
		return $this->city_id;
	}
	public function getLocationCbd() {
		return $this->cbd_id;
	}
}
?>