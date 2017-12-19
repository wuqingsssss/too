<?php
final class MCache extends Memcache{ 
	private $expire = 1800; 
    private $mem=null;
    private $http_host='';
  	public function __construct($host,$port,$http_host='') {
	
           $this->connect($host,$port);
           $this->http_host=$http_host;
  	}

	public function get($key,$needhost=true) {
		if(defined('MEM_CACHE')&&MEM_CACHE){
		  return parent::get(($needhost?$this->http_host:'').$key);}
		  else {
		  	return false;
		  }
	}

  	public function set($key, $value,$var=0,$expire='',$needhost=true) {
  		if($expire)$expire_time=(int)$expire;
  		else
  			$expire_time=$this->expire;
    	parent::set(($needhost?$this->http_host:'').$key, $value, $var, $expire_time);
  	}
  	public function delete($key,$needhost=true) {
  		return parent::delete(($needhost?$this->http_host:'').$key);
  	}
  	public function get_namespace($fix='',$needhost=true) {		
  		$np=parent::get(($needhost?$this->http_host:'').$fix);
  		if(!$np)$np=$this->reset_namespace($fix);
  		return $np;
  	}
  	public function set_namespace($fix='',$np='',$needhost=true) {
  		if(empty($np))$np=$fix.'.'.time();
  		parent::set(($needhost?$this->http_host:'').$fix, $np, 0, 0);
  	}
  	public function reset_namespace($fix='') {
  		$this->set_namespace($fix,$np);
  		return $np;
  	}
}
?>