<?php
abstract class Service {
	protected $registry;
	
	public function __construct($registry,$currentDir='') {
		$this->registry = $registry;
		if($currentDir)$this->load_config($currentDir);
	}
	
	public function __get($key) {
		return $this->registry->get($key);
	}
	
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	} 
 
    public function __call($functionName,$args){
		try{
			$this->log_sys->error($functionName.'('.print_r($args,1).')不存在;');
				
		}catch (ErrorException $e){
			echo'函数： '.$functionName.'(参数: ';
			print_r($args);
			echo')不存在！<br>\n';
		}
	}
	
	public function load_config($currentDir)
	{
		$config_name = 'config.'.$_SERVER['HTTP_HOST'].'.php';

        if(!file_exists($currentDir .'/' .$config_name)){
	     $config_name='config.php';	
         }

        require_once($currentDir .'/'.$config_name);
	}
}
?>