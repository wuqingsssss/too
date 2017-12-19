<?php
final class Request {
	public $get = array ();
	public $post = array ();
	public $cookie = array ();
	public $files = array ();
	public $server = array ();
	public function __construct() {
		$_GET = $this->clean ( $_GET );
		$_POST = $this->clean ( $_POST );
		$_REQUEST = $this->clean ( $_REQUEST );
		$_COOKIE = $this->clean ( $_COOKIE );
		$_FILES = $this->clean ( $_FILES );
		$_SERVER = $this->clean ( $_SERVER );

		if(defined('URL_MODEL')&&(URL_MODEL==1 || URL_MODEL==3)){		
		$pathinfo = pathinfo ( $_SERVER ['REQUEST_URI'] );

		if ($pathinfo ['basename']&&stripos ( $pathinfo ['dirname'], '.php?' ) === false&&stripos ( $pathinfo ['basename'], '.php?' ) === false && $pathinfo ['extension'] != 'php') {
			$rpurl=htmlspecialchars_decode($pathinfo ['dirname']. DIRECTORY_SEPARATOR.$pathinfo ['basename']);
			if($routestart=stripos ($rpurl, '.php/' )!==false){
			$routestart=(int)$routestart+5;

			$routeend=stripos ($rpurl, '?' )-$routestart;
			   if($routeend===false)
			    {
				$luyou =explode ( '&', 'route='. substr ( $rpurl,  $routestart)) ;
			    }
				else 
				{
			    $luyou =explode ( '&', 'route='. substr ( $rpurl,  $routestart,$routeend)) ;
				}
			
			foreach ( $luyou as $val ) {
				$spval = explode ( '=', $val );
				$_GET [$spval [0]] = trim($spval [1],DIRECTORY_SEPARATOR);
				$_REQUEST [$spval [0]] = trim($spval [1],DIRECTORY_SEPARATOR);
			}
			}
		}
		}
	
		$this->get = $_GET;
		$this->post = $_POST;
		$this->request = $_REQUEST;
		$this->cookie = $_COOKIE;
		$this->files = $_FILES;
		$this->server = $_SERVER;
	}
	public function clean($data) {
		if (is_array ( $data )) {
			foreach ( $data as $key => $value ) {
				unset ( $data [$key] );
				$data [$this->clean ( $key )] = $this->clean ( $value );
			}
		} else {
			$data = htmlspecialchars ( trim ( $data ), ENT_COMPAT );
		}
		
		return $data;
	}
	public function getIsNotEmpty($param) {
		return (isset ( $this->get [$param] ) && $this->get [$param] != null);
	}
	public function getIsEmpty($param) {
		return (! isset ( $this->get [$param] ) || $this->get [$param] == null);
	}
	public function postIsNotEmpty($param) {
		return (isset ( $this->post [$param] ) && $this->post [$param] != null);
	}
	public function postIsEmpty($param) {
		return (! isset ( $this->post [$param] ) || $this->post [$param] == null);
	}
	public function isDomain($str) {
		return $this->is_domain ( $str );
	}
	public function is_domain($str) {
		$preg = "/^(?:http:\/\/)?[0-9a-zA-Z]+.[0-9a-zA-Z]+_?[0-9a-zA-Z]+.(com|cn|cc|net|org|info|mobi)$/";
		
		if (preg_match ( $preg, $str )) {
			return true;
		} else {
			return false;
		}
	}
	public function cleanAddress($str) {
		return $this->clean_address ( $str );
	}
	public function clean_address($str) {
		if (preg_match_all ( '/[a-zA-Z0-9\x{4e00}-\x{9fa5}\s\-\[\]\)\(]+/u', $str, $m )) {
			return implode ( $m [0] );
		} else
			return '';
	}
	public function isAddress($str) {
		return $this->is_address ( $str );
	}
	public function is_address($str) {
		// $preg = "/^(?!_)(?!.*?_$)[a-zA-Z0-9\u4e00-\u9fa5\s（）()-\[\]]+$/u";//js不兼容php
		$preg = '/^(?!_)(?!.*?_$)[a-zA-Z0-9\x80-\xff\s\(\)\-\[\]]+$/'; // 全角字符
		$preg = "/^(?!_)(?!.*?_$)[a-zA-Z0-9\x{4e00}-\x{9fa5}（）【】、，——\s\(\)\-\[\]]+$/u"; // 汉字utf8
		
		if (preg_match ( $preg, $str )) {
			return true;
		} else {
			return false;
		}
	}
	public function isUsername($str) {
		return $this->is_username ( $str );
	}
	public function is_username($str) {
		$preg = "/^[A-Za-z][0-9A-Za-z_]{3,14}$/";
		if (preg_match ( $preg, $str )) {
			return true;
		} else {
			return false;
		}
	}
	public function isPhone($str) {
		return $this->is_phone ( $str );
	}
	public function is_phone($str) {
		$preg = "/^(13|14|15|17|18)[0-9]{9}$/";//([\d\+]{3,5})?1\d{10}
		if (preg_match ( $preg, $str )) {
			return true;
		} else {
			return false;
		}
	}

	public function isTel($str) {
		return $this->is_tel ( $str );
	}
	public function is_tel($str) {
		$preg = "/^0\d{2,3}-?\d{7,8}$/";
		if (preg_match ( $preg, $str )) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * 验证输入的邮件地址是否合法
	 *
	 * @access public
	 * @param string $email
	 *        	需要验证的邮件地址
	 *        	
	 * @return bool
	 */
	public function isEmail($str) {
		return $this->is_email ( $str );
	}
	public function is_email($user_email) {
		$chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
		if (strpos ( $user_email, '@' ) !== false && strpos ( $user_email, '.' ) !== false) {
			if (preg_match ( $chars, $user_email )) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	public function getIp() {
		if ($this->server ["HTTP_X_FORWARDED_FOR"]) {
			$ip = $this->server ["HTTP_X_FORWARDED_FOR"];
		} elseif ($this->server ["HTTP_CLIENT_IP"]) {
			$ip = $this->server ["HTTP_CLIENT_IP"];
		} elseif ($this->server ["REMOTE_ADDR"]) {
			$ip = $this->server ["REMOTE_ADDR"];
		} elseif (getenv ( "HTTP_X_FORWARDED_FOR" )) {
			$ip = getenv ( "HTTP_X_FORWARDED_FOR" );
		} elseif (getenv ( "HTTP_CLIENT_IP" )) {
			$ip = getenv ( "HTTP_CLIENT_IP" );
		} elseif (getenv ( "REMOTE_ADDR" )) {
			$ip = getenv ( "REMOTE_ADDR" );
		} else {
			$ip = "Unknown";
		}
		return $ip;
	}
}
?>