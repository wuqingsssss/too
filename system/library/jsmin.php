<?php
final class JSMin {
  const ORD_LF    = 10;
  const ORD_SPACE = 32;
 
  protected $a           = '';
  protected $b           = '';
  protected $input       = '';
  protected $inputcss       = '';
  protected $inputIndex  = 0;
  protected $inputLength = 0;
  protected $lookAhead   = null;
  protected $output      = '';
  protected $outputcss      = '';
  protected $timer,$log ;

  // -- Public Static Methods --------------------------------------------------
  /*带样式js暂不能压缩*/
  public static function minjs($js,$path='',$type=0) {
    $jsmin = new JSMin(); 
    if(!is_array($js)&&$type){
    	$js=explode(',', $js);
    }else {
    	$js=array($js);
    }
    $jsmin->log->info($jsmin->timer->time('jsmin01'));
    foreach($js as $jsitem)
    {
    	if(!empty($jsitem))
    $jsmin->add_js($jsitem,$type);   	
    }
    $jsmin->log->info($jsmin->timer->time('jsmin02'));
    if(!empty($path)){
    	$jsmin->log->info($jsmin->timer->time('jsmin-md5-s'));
    $putfile=$path.md5($jsmin->input).'.js';
        $jsmin->log->info($jsmin->timer->time('jsmin-md5-d'));
    if(!empty($jsmin->input)){
    	if(!file_exists($putfile))
    	{ 
    	$jsmin->log->info($jsmin->timer->time('jsmin1'));
       $output=$jsmin->min();
       $jsmin->log->info($jsmin->timer->time('jsmin2'));
    	//$output=	$jsmin->zipJs(); 
    	
      
    file_put_contents($putfile,$output);  
     $oss=new OSS();
     $oss->upload_file_by_file($oss->open_bucket,$putfile,DIR_ROOT.$putfile);
    
    	}
    return '<script type="text/javascript" src="'.$putfile.'"></script>';
    }
    }
    else
    {   
    	$output=$jsmin->min();
    	//$output=	$jsmin->zipJs();
    	
    return $output;
    }
  }
  public static function mincss($css,$path='',$type=0) {
  	$jsmin = new JSMin();
  	if(!is_array($css)&&$type){
  		$css=explode(',', $css);
  	}else {
    	$css=array($css);
    }
  	foreach($css as $cssitem)
  	{if(!empty($cssitem))
  		$jsmin->add_css($cssitem,$type);
  	}
  	
  	if(!empty($path)){
  		if(!empty($jsmin->outputcss)){
  			$putfile=$path.md5($jsmin->outputcss).'.css';
  			if(!file_exists($putfile))
  			{$jsmin->log->info($jsmin->timer->time('cssmin1'));
  				$output=$jsmin->cssmin();
  				$jsmin->log->info($jsmin->timer->time('cssmin1'));
  			file_put_contents($putfile,$output);
  			$alioss=new ALIOSS();
  			$alioss->upload_file_by_file($oss->open_bucket,$putfile,DIR_ROOT.$putfile);	
  			}
  			return '<link rel="stylesheet" type="text/css"
	href="'.$putfile.'" />';
  		}
  	}
  	else
  	{
  	$output=$jsmin->cssmin();
  	return $output;
  	}
  	
  }
  // -- Public Instance Methods ------------------------------------------------
 
  public function __construct($input='') {
  	$this->timer=new runtime();
  	$this->timer->start();
  	$this->log=$GLOBALS['log_sys'];
  }
  // -- Protected Instance Methods ---------------------------------------------
  public function add_js($js,$type) {	
  	$this->input       .= str_replace("\r\n", "\n",($type?file_get_contents($js):$js));
  	$this->inputLength = strlen($this->input);	
  }
  public function add_css($css,$type) {
  	$this->outputcss       .= str_replace(array("\r\n", "\r", "\n"), "",$type? file_get_contents($css):$css);
  	$this->inputLength = strlen($this->outputcss);
  }
  public function cssmin($css) {
  	return $this->outputcss;
  }
  protected function action($d) {
    switch($d) {
      case 1:
        $this->output .= $this->a;
 
      case 2:
        $this->a = $this->b;
 
        if ($this->a === "'" || $this->a === '"') {
          for (;;) {
            $this->output .= $this->a;
            $this->a       = $this->get();
 
            if ($this->a === $this->b) {
              break;
            }
 
            if (ord($this->a) <= self::ORD_LF) {
              throw new JSMinException('Unterminated string literal.');
            }
 
            if ($this->a === '\\') {
              $this->output .= $this->a;
              $this->a       = $this->get();
            }
          }
        }
 
      case 3:
        $this->b = $this->next();
 
        if ($this->b === '/' && (
            $this->a === '(' || $this->a === ',' || $this->a === '=' ||
            $this->a === ':' || $this->a === '[' || $this->a === '!' ||
            $this->a === '&' || $this->a === '|' || $this->a === '?')) {
 
          $this->output .= $this->a . $this->b;
 
          for (;;) {
            $this->a = $this->get();
 
            if ($this->a === '/') {
              break;
            } elseif ($this->a === '\\') {
              $this->output .= $this->a;
              $this->a       = $this->get();
            } elseif (ord($this->a) <= self::ORD_LF) {
              throw new JSMinException('Unterminated regular expression '.
                  'literal.');
            }
 
            $this->output .= $this->a;
          }
 
          $this->b = $this->next();
        }
    }
  }
 
  protected function get() {
    $c = $this->lookAhead;
    $this->lookAhead = null;
 
    if ($c === null) {
      if ($this->inputIndex < $this->inputLength) {
        $c = $this->input[$this->inputIndex];
        $this->inputIndex += 1;
      } else {
        $c = null;
      }
    }
 
    if ($c === "\r") {
      return "\n";
    }
 
    if ($c === null || $c === "\n" || ord($c) >= self::ORD_SPACE) {
      return $c;
    }
 
    return ' ';
  }
 
  protected function isAlphaNum($c) {
    return ord($c) > 126 || $c === '\\' || preg_match('/^[\w\$]$/', $c) === 1;
  }
 
  protected function min() {
    $this->a = "\n";
    $this->action(3);
 
    while ($this->a !== null) {
      switch ($this->a) {
        case ' ':
          if ($this->isAlphaNum($this->b)) {
            $this->action(1);
          } else {
            $this->action(2);
          }
          break;
 
        case "\n":
          switch ($this->b) {
            case '{':
            case '[':
            case '(':
            case '+':
            case '-':
              $this->action(1);
              break;
 
            case ' ':
              $this->action(3);
              break;
 
            default:
              if ($this->isAlphaNum($this->b)) {
                $this->action(1);
              }
              else {
                $this->action(2);
              }
          }
          break;
 
        default:
          switch ($this->b) {
            case ' ':
              if ($this->isAlphaNum($this->a)) {
                $this->action(1);
                break;
              }
 
              $this->action(3);
              break;
 
            case "\n":
              switch ($this->a) {
                case '}':
                case ']':
                case ')':
                case '+':
                case '-':
                case '"':
                case "'":
                  $this->action(1);
                  break;
 
                default:
                  if ($this->isAlphaNum($this->a)) {
                    $this->action(1);
                  }
                  else {
                    $this->action(3);
                  }
              }
              break;
 
            default:
              $this->action(1);
              break;
          }
      }
    }
 
    return $this->output;
  }
 
  function zipJs(){
  	$js=$this->input;
  	$h1 = 'http://';
  	$s1 = '【:??】';    //标识“http://”,避免将其替换成空
  	$h2 = 'https://';
  	$s2 = '【s:??】';    //标识“https://”
  	preg_match_all('#include\("([^"]*)"([^)]*)\);#isU',$js,$arr);
  	if(isset($arr[1])){
  		foreach ($arr[1] as $k=>$inc){
  			$path = "http://www.xxx.com/";          //这里是你自己的域名路径
  			$temp = file_get_contents($path.$inc);
  			$js = str_replace($arr[0][$k],$temp,$js);
  		}
  	}
  
  	$js = preg_replace('#function include([^}]*)}#isU','',$js);//include函数体
  	$js = preg_replace('#\/\*.*\*\/#isU','',$js);//块注释
  	$js = str_replace($h1,$s1,$js);
  	$js = str_replace($h2,$s2,$js);
  	$js = preg_replace('#\/\/[^\n]*#','',$js);//行注释
  	$js = str_replace($s1,$h1,$js);
  	$js = str_replace($s2,$h2,$js);
  	$js = str_replace("\t","",$js);//tab
  	$js = preg_replace('#\s?(=|>=|\?|:|==|\+|\|\||\+=|>|<|\/|\-|,|\()\s?#','$1',$js);//字符前后多余空格
  	$js = str_replace("\t","",$js);//tab
  	$js = str_replace("\r\n","",$js);//回车
  	$js = str_replace("\r","",$js);//换行
  	$js = str_replace("\n","",$js);//换行
  	$js = trim($js," ");
  	$this->output=$js;
  	return $js;
  }
  
  
  protected function next() {
    $c = $this->get();
 
    if ($c === '/') {
      switch($this->peek()) {
        case '/':
          for (;;) {
            $c = $this->get();
 
            if (ord($c) <= self::ORD_LF) {
              return $c;
            }
          }
 
        case '*':
          $this->get();
 
          for (;;) {
            switch($this->get()) {
              case '*':
                if ($this->peek() === '/') {
                  $this->get();
                  return ' ';
                }
                break;
 
              case null:
                throw new JSMinException('Unterminated comment.');
            }
          }
 
        default:
          return $c;
      }
    }
 
    return $c;
  }
 
  protected function peek() {
    $this->lookAhead = $this->get();
    return $this->lookAhead;
  }
}
 
// -- Exceptions ---------------------------------------------------------------
class JSMinException {}
//class JSMinException extends Exception {}
?>