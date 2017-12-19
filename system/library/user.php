<?php
final class User {
	private $user_id;
	private $username;
  	private $permission = array();
  	private $reqcontroller;
  	private $user_group_id;
  	private $is_admin;
  	private $super_admin;

  	public function __construct($registry) {
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		$this->reqcontroller=substr($this->request->get['route'], 0,strripos($this->request->get['route'],'/'));
		$user_id = $this->session->data['user_id'];
		if (isset($user_id)) {
			$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$this->session->data['user_id'] . "' AND status = '1'");

			if ($user_query->num_rows) {
				$this->user_id = $user_query->row['user_id'];
				$this->username = $user_query->row['username'];
				
      			$this->db->query("UPDATE " . DB_PREFIX . "user SET ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE user_id = '" . (int)$this->session->data['user_id'] . "'");
      			$this->user_group_id = $user_query->row['user_group_id'];
      			$this->is_admin = $user_query->row['is_admin'];

				//超级管理员权限
				if($this->user_group_id=='1'){
					$this->permission['super_admin'] =1 ;
					$this->super_admin=true;
				}else {
					
					$user_group_query = $this->db->query("SELECT permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$this->user_group_id . "'");
					
					$this->permission = unserialize($user_group_query->row['permission']);
					$this->super_admin=false;
				}
				
			} else {
				$this->logout();
			}
    	}
  	}
		
  	public function login($username, $password) {
    	$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE username = '" . $this->db->escape($username) . "' AND password = '" . $this->db->escape(md5($password)) . "' AND status = '1'");

    	if ($user_query->num_rows) {
			$this->session->data['user_id'] = $user_query->row['user_id'];
			
			$this->user_id = $user_query->row['user_id'];
			$this->username = $user_query->row['username'];			
			$this->user_group_id = $user_query->row['user_group_id'];
			$this->is_admin = $user_query->row['is_admin'];
			//超级管理员权限
			if($this->user_group_id=='1'){
				$this->permission['super_admin'] = 1;
				$this->super_admin=true;
			}else{
      		$user_group_query = $this->db->query("SELECT permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$this->user_group_id . "'");

	  		$this->permission = unserialize($user_group_query->row['permission']);
	  		$this->super_admin=false;
			}
	  		
	  		
/*
			if (is_array($permissions)) {
				foreach ($permissions as $key => $value) {
					$this->permission[$key] = $value;
				}
			}*/
		
      		return true;
    	} else {
      		return false;
    	}
  	}

  	public function logout() {
		unset($this->session->data['user_id']);
	
		$this->user_id = '';
		$this->username = '';
		
		session_destroy();
  	}
  	
  	//菜君权限检测方法
  	public function chkPermission($action='', $reroute='') {
		
  		if (isset ( $this->permission ['super_admin'] )&&$this->permission ['super_admin']){
			return true;
  		}

		if (empty ( $reroute ))
			$reroute = $this->reqcontroller;

		if ($action) {
			return isset ( $this->permission [$reroute] [$action] ) && $this->permission [$reroute] [$action];
		} else {
			return isset ( $this->permission [$reroute] );
		}
  	}
  	
  	
	//老方法兼容到新的权限管理
  	public function hasPermission($action='', $reroute='') {
		return $this->chkPermission($action,$reroute);

  	}
  	//哀乐方法废弃 兼容新方法写法
  	public function permit($permCode) {
  		return true;
  		if (isset ( $this->permission ['super_admin'] )&&$this->permission ['super_admin']){
  			return true;
  		}
		$userPermissions = $this->permission;
		if (isset($this->permission)) {
	  		return in_array($permCode, $userPermissions);
		} else {
	  		return false;
		}
  	}	//哀乐方法废弃 兼容新方法写法
  	public function permitAnd($permCodes) {
  		return true;
  		if (isset ( $this->permission ['super_admin'] )&&$this->permission ['super_admin']){
  			return true;
  		}
		$userPermissions = $this->permission;
		if (isset($userPermissions)) {
			$diff=array_diff($permCodes,$userPermissions);
	  		return empty($diff);
		} else {
	  		return false;
		}
  	}	//哀乐方法废弃 兼容新方法写法
  	public function permitOr($permCodes) {
  		return true;
  		if (isset ( $this->permission ['super_admin'] )&&$this->permission ['super_admin']){
  			return true;
  		}
		$userPermissions = $this->permission;
		if (isset($userPermissions)) {
	  		foreach($permCodes as $code){
				if(in_array($code,$userPermissions)){
					return true;
				}
			}
			return false;
		} else {
	  		return false;
		}
  	}

  	public function isLogged() {
    	return $this->user_id;
  	}
  
  	public function getId() {
    	return $this->user_id;
  	}
  	public function getGroupId() {
  		return $this->user_group_id;
  	}
  
  	public function getUserName() {
  		return $this->username;
  	}
  	public function getUserPermissions() {
    	return $this->permission;
  	}	
  	public function isAdmin() {
  		return $this->is_admin;
  	}
  	public function isSuperAdmin(){
  	return $this->super_admin;
  	}
}