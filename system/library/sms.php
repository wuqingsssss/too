<?php
final class Sms {
	protected $URL;
	protected $key;
	protected $open;
	protected $user;
	protected $smspaths;
	protected $registry;
	protected $dissmspath;
	public function __construct($registry) {
		if(!is_array($registry))
		$this->registry =  getRegistry ();	
		else 
		$this->registry = $registry;
		$this->load->model('setting/extension');
		$this->smspaths=array();
		$sms=$this->model_setting_extension->getExtensions('sms');
		foreach($sms as $ex){
			if($this->chk_smspath($ex['code'])){
			$this->smspaths[$ex['code']]= $this->config->get($ex['code'] . '_sort_order');
			}
		}
		if(!$this->smspaths){
			if($this->log_sys)$this->log_sys->warn('Sms::no_smspaths');
		}
		asort($this->smspaths);
	}
	public function __get($key) {
		return $this->registry->get($key);
	}
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	public function chk_smspath($code){
		if($this->mem){//检测当前通道是否正常
			$dis= $this->mem->get('sms_dissmspath_'.$code);
			if($dis) return false;
		}
		return true;
	}
	public function set_unable_smspath($code){
		if($this->mem&&count($this->smspaths)>1){//如果通道异常则 增加禁止标记 缓存300秒
			$this->mem->set('sms_dissmspath_'.$code,$code,0,300);
			unset($this->smspaths[$code]);
			if($this->log_sys)$this->log_sys->warn('sms_dissmspath_'.$code);
		}
	}
	public function send($mobile,$msg,$path=''){
		if($this->smspaths){
		if($path&&$this->smspaths[$path]){
			$this->load->service('sms/'.$path.'/sms');
			$res=$this->{'service_sms_'.$path.'_sms'}->send($mobile,$msg);
			if($res)
			{ 
				return true;
			}
			else 
			{
				$this->set_unable_smspath($path);
			}
		}
		foreach($this->smspaths as $smspath=> $sortorder){
			$this->load->service('sms/'.$smspath.'/sms');
			$res=$this->{'service_sms_'.$smspath.'_sms'}->send($mobile,$msg);
			if($res){ return true;}
			else
			{
				$this->set_unable_smspath($smspath);
			}
		}
	}
	return false;
	}
}

?>