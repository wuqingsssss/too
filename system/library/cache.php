<?php
final class Cache { 
	private $expire = 3600; 

  	public function __construct() {
		$files = glob(DIR_CACHE . 'cache.*');
		
		if ($files) {
			foreach ($files as $file) {
				$time = substr(strrchr($file, '.'), 1);

      			if ($time < time()) {
					if (file_exists($file)) {
						unlink($file);
						clearstatcache();
					}
      			}
    		}
		}
  	}

	public function get($key) {
		$files = glob(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');

		if ($files) {
			$cache = file_get_contents($files[0]);
			return unserialize($cache);
		}
		else 
			return false;
	}

  	public function set($key, $value,$expire='') {
    	$this->delete($key);
		if($expire)$expire_time=$expire;
		else 
		$expire_time=time() + $this->expire;
		$file = DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.' . ($expire_time);
    	if(!file_exists($file)){
			$handle = fopen($file, 'w');
	
	    	fwrite($handle, serialize($value));
			
	    	fclose($handle);
    	}
  	}
	
  	public function delete($key) {
		$files = glob(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');
		
		if ($files) {
    		foreach ($files as $file) {
      			if (file_exists($file)) {
					unlink($file);
					clearstatcache();
				}
    		}
		}
  	}
}
?>