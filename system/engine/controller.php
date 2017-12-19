<?php
abstract class Controller {
	protected $registry;	
	protected $id;
	protected $layout;
	protected $template;
	protected $children = array();
	protected $data = array();
	protected $rules = array();
	protected $output;
	protected $errors= array();
	public function __construct($registry,$data=array()) {
		$this->registry = $registry;
		$this->data=$data;
        $this->data['route']=$this->request->get['route'];
		$language=$registry->get('language');
		if(isset($language))
			$this->load_language('help/guide');	
	}
	
	public function __get($key) {
		return $this->registry->get($key);
	}
	
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}
	
	
	public function __call($functionName,$args){

		try{
			
			call_user_func ( array (
					$this,
					"_$methodName"
			), $arguments );
				
		}catch (Exception $e){
			echo'函数： '.$functionName.'(参数: ';
			print_r($args);
			echo')不存在！<br>\n';
		}
		
	}
	
    public function noTimeout($time=0,$abort=TRUE){
    	ignore_user_abort($abort); //如果客户端断开连接，不会引起脚本abort
    	ini_set("max_execution_time", $time);
    }
	public function getMethd($methd,$args) {
		return $this->$methd($args);
	}
	
	public function getChildren() {
		return $this->children;
	}
	
	protected function forward($route, $args = array()) {
		return new Action($route, $args);
	}

	protected function redirect($url, $status = 302) {
		header('Status: ' . $status);
		// TODO some problem when redirect.
	    // $this->setback(true,HTTP_CATALOG.trim($this->request->server['REQUEST_URI'],'/'));
		
		if($this->startsWith($url,'http')!=1){
			header('Location: '.HTTP_SERVER. str_replace('&amp;', '&', $url));
		}else{
			header('Location: '. str_replace('&amp;', '&', $url));
		}
		exit();
	}
	//清空当前回调地址
	protected function clearback() {
		unset($this->session->data['REFERER']);	
	}
	
	//目标页设置父级url扩展方法
	protected function setbackparent($rewrite=true,$url='',$noexpect=array()) {
		
		if(!$url){
			  //$url = 'http://'.WEB_HOST.$this->request->server['REQUEST_URI'];
			 $url=$this->request->server['HTTP_REFERER'];
		}
		$this->setback($rewrite,$url,$noexpect);
		
	}
	//发起页面预设回调地址方法
	protected function setback($rewrite=true,$url='',$noexpect=array()) {

		if(!$url){
		    $url = 'http://'.WEB_HOST.$this->request->server['REQUEST_URI'];
		   // $url=$this->request->server['HTTP_REFERER'];
		}
		    
		if($rewrite||!$this->session->data['REFERER'])
		    $this->session->data['REFERER']= $url;
		if($noexpect)
		{
			foreach($noexpect as $item)
			{
				if(stripos($this->session->data['REFERER'], $item)!==false)
				{
					unset($this->session->data['REFERER']);
					break;
				}
			}
		}
		
	}
	//跳转至已设置的回调地址，外站地址不跳转
	protected function goback($url='') {
	    
		if(!$url){
		    $url=isset($this->session->data['REFERER'])
		         &&(stripos($this->session->data['REFERER'], $this->request->server['REQUEST_URI']) === false)
		         &&(stripos($this->session->data['REFERER'], HTTP_SERVER) !== false || stripos($this->session->data['REFERER'], HTTPS_SERVER) !== false)?$this->session->data['REFERER']:$this->url->link('common/home', '', 'SSL');
		}
		
		$this->redirect($url);
	}

	protected function getChild($child, $args = array()) {
		$action = new Action($child, $args);
		$file = $action->getFile();
		$class = $action->getClass();
		$method = $action->getMethod();
	
	
		if (file_exists($file)) {
			require_once($file);

			$controller = new $class($this->registry,$this->data);
			
			$controller->$method($args);
			
			return $controller->output;
		} else {
			exit('Error: Could not load controller ' . $child . '!');
		}		
	}
	
	protected function getChildMethod($child, $args = array()) {
	    $action = new Action($child, $args);
	    $file = $action->getFile();
	    $class = $action->getClass();
	    $method = $action->getMethod();
	
	
	    if (file_exists($file)) {
	        require_once($file);
	
	        $controller = new $class($this->registry,$this->data);
	        	
	        return $controller->$method($args);
	    } else {
	        exit('Error: Could not load controller ' . $child . '!');
	    }
	}
	
	protected function render() {
		if ($this->layout) {
    		return $this->render_with_layout();
		}else{
			return $this->render_old();
		}
	}
	
	// we keeep this method just make all old opencart part work with shopilex as well
	private function render_old() {
		foreach ($this->children as $child) {
			$this->data[basename($child)] = $this->getChild($child);
		}
	
		if (file_exists(DIR_TEMPLATE . $this->template)) {
			extract($this->data);
				
			ob_start();
	
			require(DIR_TEMPLATE . $this->template);
	
			$this->output = ob_get_contents();
	
			ob_end_clean();
	
			return $this->output;
		} else {
			exit('Error: Could not load template ' . DIR_TEMPLATE . $this->template . '!');
		}
	}
	
	protected function html($output,$path,$rename) {
		$path=DIR_ROOT.'static/'.$path.'/';
		if(!file_exists($path)){
			mkdir($path, 0777);
		}
		$path=$path.$rename.'.shtml';
		
		if(!file_exists($path)){
			$file=fopen($path,'w');
			fwrite($file,$output);
			fclose($file);
		}
	}
	
	protected function checkHtml($path,$rename) {
		$path=DIR_ROOT.'static/'.$path.'/';
		if(!file_exists($path)){
			mkdir($path, 0777);
		}
		$path=$path.$rename.'.shtml';
	
		if(file_exists($path))
			return true;
		else 	
			return false;
	
	}
	
	private function render_with_layout() {
		$file = DIR_TEMPLATE.$this->template;
		
		if (file_exists($file)) {
			extract($this->data);
				
			ob_start();
	
			require($file);
	
			$this->output = ob_get_contents();
	
			ob_end_clean();

		} else {
			exit('Error: Could not load template 11 ' . DIR_TEMPLATE . $this->template . '!');
		}
		 
		if ($this->layout) {
			$file  = DIR_APPLICATION . 'controller/' . $this->layout . '.php';
			$class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $this->layout);
				
			if (file_exists($file)) {
				require_once($file);
	
				$controller = new $class($this->registry);
	
	
				$controller->data[$this->id] = $this->output;
	
				$controller->init();
				
				foreach ($controller->getChildren() as $child) {
					$controller->data[basename($child)] = $this->getChild($child);
				}
				
				$controller->before($this->data);
				 
				$controller->excute();
	
				$this->output = $controller->output;
	
				$controller->after($this->data);
			} else {
				exit('Error: Could not load controller ' . $this->layout . '!');
			}
		}
	
		$this->response->setOutput($this->output);
	}
	
	/*
	added this function, so could load language to display with less code.
	*/
	protected function load_language($filename) {
		$this->languages = $this->registry->get('language');
		$temp =array();
		$temp = $this->languages->load($filename);
	
		foreach($temp as  $key=>$val ){
			// all language messge for success or error or warning would not auto load.
	
			if($this->startsWith($key,'error_')!=1 && $this->startsWith($key,'success_')!=1 && $this->startsWith($key,'warning_')!=1){
				$this->data[$key]=$val;
			}
		}
	
	}
	
	protected function processerror($errors,$msg) {
		$data=array();
		foreach ($errors as $error) {
			if($this->startsWith($error,'arr_')!=1){
				if (isset($msg[$error])) {
					$this->data['error_'.$error] =$msg[$error];
				} else {
					$this->data['error_'.$error] = '';
				}
			}else{
				$error=str_replace('arr_', '', $error);
				if (isset($msg[$error])) {
					$this->data['error_'.$error] = $msg[$error];
				} else {
					$this->data['error_'.$error] = array();
				}
			}
		}
	}
	
	protected function pagination($total,$page,$config_limit,$url) {
		$pagination = new Pagination();
		$pagination->total = $total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get($config_limit);
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url =$url;
		return $pagination->render();
	}
	

	protected function startsWith($Haystack, $Needle){
		// Recommended version, using strpos
		return strpos($Haystack, $Needle) === 0;
	}
	
	/*
	 * @filename
	 * @args tpl页面中所用到的参数
	 * */
	protected function common_render_tpl($filename,$arg=array()){
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/'.$filename)) {
			$file= DIR_TEMPLATE.$this->config->get('config_template') . '/template/'.$filename;
		} else {
			$file = DIR_TEMPLATE.'default/template/'.$filename;
		}
		
		$this->data=array_merge($this->data,$arg);
		
		if (file_exists($file)) {
			extract($this->data);

      		ob_start();

	  		require($file);

	  		$content = ob_get_contents();

      		ob_end_clean();

      		return $content;
    	} else {
      		exit('Error: Could not load template ' . $file . '!');
    	}
	}

	public function renderFront($template){
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/'.$template)) {
			$this->template = $this->config->get('config_template') . '/template/'.$template;
		} else {
			$this->template = 'default/template/'.$template;
		}

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

		$this->response->setOutput($this->render());
	}

	public function renderSection($template){
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/'.$template)) {
			$this->template = $this->config->get('config_template') . '/template/'.$template;
		} else {
			$this->template = 'default/template/'.$template;
		}

		$this->render();
	}
}
?>