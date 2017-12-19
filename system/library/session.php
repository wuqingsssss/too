<?php
final class Session {
	public $data = array();
		protected $mem;	
  	public function __construct() {		
  		global $mem;
  		$this->mem=$mem;
  		$memdata=array();
		if (!session_id()) {
			ini_set('session.use_cookies', 'On');
			ini_set('session.use_trans_sid', 'Off');
			
			session_set_cookie_params(0, '/');
			session_start();
		}

	   if($this->mem){
		  $memdata = $this->mem->get('_session_'.session_id());
	   }
		if($memdata){
		$_SESSION=array_merge($_SESSION,$memdata);
		}
		$this->data =& $_SESSION;
		
		
	}
	
	public function __destruct(){
		if($this->mem){
			$this->mem->set('_session_'.session_id(),$this->data,0,1800);
		}
	}
}
?>