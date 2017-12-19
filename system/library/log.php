<?php
final class Log {
    private static $maxFileSize = 20428800;//50MB

    private $filename;
    private $filename_time;
    private $config;

    public function __construct($config, $filename='log.log', $filename_time = '') {
        $this->config = $config;
        $this->filename = $filename;
        $this->filename_time = $filename_time;
    }

    public function write($message,$filename="error.log") {
    	   	
    	if(!is_dir(DIR_LOGS .DIR_DIR)) mkdir(DIR_LOGS .DIR_DIR); 
        $file = DIR_LOGS .DIR_DIR. $filename;

        if(file_exists($file)&&filesize($file) > self::$maxFileSize){       
        	$rfile=DIR_LOGS  .DIR_DIR. basename($file,'.log').date('YmdGis').'.log';
        	rename($file, $rfile);
        }      
        $handle = fopen($file, 'a+');
        $route  = isset($_REQUEST['route'])? $_REQUEST['route']:'';
        
        fwrite($handle, date('Y-m-d G:i:s').'::'.DIR_DIR.'::'.$route.'::'. $message . "\n");
        fclose($handle);
    }
  
    public function log($message,$filename="") {
    	if(empty($filename)){
    	         if(defined('LOG_FILE')&&LOG_FILE) $filename=LOG_FILE;
    	         else 
    	         {	
    	         	$filename='log.log';
    	         }
    	}
    	
    	         
    	 $this->write($message,$filename);
    	 
    }
    
    public function error($message,$filename="") {
    	if(empty($filename)){
    	if(defined('LOG_ERROR')&&LOG_ERROR)
    		 $filename=LOG_ERROR;
    	else
    	{
    		$filename='error.log';
    	}
    	}
    	
    	 $this->write($message,$filename);  	
    }

    public function log_time($message) {
    
    	if ($this->config->get('config_debug')||defined('DEBUG')&& DEBUG) {
    		
    		$filename='log_time.log';
    		if(defined('LOG_TIME')&&LOG_TIME) $filename=LOG_TIME;
    		
    		$this->write($message,$filename);
    	}
    } 
    public function debug($message,$filename = 'debug.log') {
    	if ($this->config->get('config_debug')||defined('DEBUG')&& DEBUG) {    		
    		$this->write($message,$filename);
    	}
    }

    public function sql($rawSql) {
    	$filename =  'sql.log';
    	if(defined('LOG_SQL')&&LOG_SQL) $filename=LOG_SQL;
    	
    	$user = getRegistry()->get('user');
    	if (isset($user) && $this->need_to_log_sql($rawSql)) {
    		$userName = $user->getUserName() . "[" . $user->getId() . "]";
    		$message = date('Y-m-d G:i:s') . "- {$userName} - " . $rawSql . "\n";
    		$message.=$this->print_stack_trace();
    
    		$this->write($message,$filename);
    
    	}
    }
    private function deleteOldContent($file) {
        if (!file_exists($file)) return;
        if (filesize($file) > self::$maxFileSize) {
            $fh = fopen($file, 'w');
            fclose($fh);
        }
    }

    private function print_stack_trace() {
        $stackTrace='';
        $array = debug_backtrace();
        //print_r($array);//信息很齐全
        unset($array[0]);
        foreach ($array as $row) {
            $stackTrace .= '    '. $row['file'] . ':' . $row['line'] . ', method: ' . $row['function'] . "\n";
        }
        return $stackTrace;
    }
    
    private function need_to_log_sql($sql){
        return preg_match('/ts_order/i',$sql) && preg_match('/update/i',$sql) && preg_match('/order_status_id/i',$sql);
    }

   
}
?>