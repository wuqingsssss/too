<?php
final class Loader {
	protected $registry;
	
	public function __construct($registry) {
		$this->registry = $registry;
	}
	
	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}
	
	public function library($library) {
		$file = DIR_SYSTEM . 'library/' . $library . '.php';
		
		if (file_exists($file)) {
			include_once($file);
		} else {
			exit('Error: Could not load library ' . $library . '!');
		}
	}
	
	public function rule($rule='') {
		$data = array();
		if($rule=='')
			$file = DIR_SYSTEM . 'config/rules.php';
		else 
			$file = DIR_SYSTEM . 'config/'.$rule.'.php';
		
		if (file_exists($file)) {
			$_ = array();
			 
			require($file);
			
			$data = array_merge($data, $_);
			
			return $data;
			
		} else {
			exit('Error: Could not load rule ' . $rule . '!');
		}
	}
	
	public function model($model,$app_path='') {
		
		if($app_path!=''){
			$file  = DIR_ROOT . '/'.$app_path.'/model/' . $model . '.php';
		}elseif(defined('DIR_APPINTCFACE') && DIR_APPINTCFACE!=''){
			
			$file  = DIR_APPINTCFACE.'model/' . $model . '.php';
			
		}else{
			$file  = DIR_APPLICATION .'model/' . $model . '.php';
		}
		

		$class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);
		
		if (file_exists($file)) {
			include_once($file);
			
			$this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));
		} else {
			exit('Error: Could not load model ' . $model . '!');
		}
	}
	 

	public function service($model,$app_path='') {
		
		if($app_path!=''){
			$file  = DIR_ROOT . '/'.$app_path.'/service/' . $model . '.php';
		}elseif(defined('DIR_APPINTCFACE') && DIR_APPINTCFACE!=''){
				
			$file  = DIR_APPINTCFACE.'service/' . $model . '.php';
				
		}else{
			$file  = DIR_APPLICATION .'service/' . $model . '.php';
		}
		
		$class = 'Service' . preg_replace('/[^a-zA-Z0-9]/', '', $model);
		
		if (file_exists($file)) {
			include_once($file);
			
			$this->registry->set('service_' . str_replace('/', '_', $model), new $class($this->registry));
		} else {
			exit('Error: Could not load service ' . $model . '!');
		}
	}

	public function database($driver, $hostname, $username, $password, $database, $prefix = NULL, $charset = 'UTF8') {
		$file  = DIR_SYSTEM . 'database/' . $driver . '.php';
		$class = 'Database' . preg_replace('/[^a-zA-Z0-9]/', '', $driver);
		
		if (file_exists($file)) {
			include_once($file);
			
			$this->registry->set(str_replace('/', '_', $driver), new $class());
		} else {
			exit('Error: Could not load database ' . $driver . '!'); 
		}
	}
	
	public function config($config) {
		$this->config->load($config);
	}
	
	public function language($language) {
		return $this->language->load($language);
	}
} 
?>