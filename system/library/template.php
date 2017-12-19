<?php
final class Template {
	public $data = array();
	
	public function fetch($filename) {
		$file = DIR_TEMPLATE . $filename;
    //	global $vupgrade; $file = $vupgrade->vPath($file);
		if (file_exists($file)) {
			extract($this->data);
			
      		ob_start();
      
	  		include($file);
      
	  		$content = ob_get_contents();

      		ob_end_clean();

      		return $content;
    	} else {
      		exit('Error: Could not load template ' . $file . '!');
    	}	
	}
}
?>