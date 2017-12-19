<?php
final class Oss {
    protected  $oss;
    protected $registry;
	public function __construct($registry) {
		if(!is_array($registry))
		$this->registry =  getRegistry ();	
		else 
		$this->registry = $registry;

		$this->registry->load->service ('alibaba/alioss' ,'service');
		$this->oss=$this->registry->service_alibaba_alioss;
	}
	public function __get($key) {
		return $this->oss->{$key};
	}
	public function __set($key, $value) {
		$this->oss->{$key}=$value;
	}
	
	public function __call($methodName, $arguments) {
		return call_user_func_array(
				array($this->oss, $methodName),
				$arguments
		);
	}


}

?>