<?php
final class Http {
	
	/* 远程请求异步方法 */
	public static function mgetSSCGET($url,$post_data, $return = false,$timeout=30) {
	
	
		$url2 = parse_url ( $url );
	
		$url2 ["path"] = ($url2 ["path"] == "" ? "/" : $url2 ["path"]);
		$url2 ["port"] = ($url2 ["port"] == "" ? 80 : $url2 ["port"]);
		$host_ip = @gethostbyname ( $url2 ["host"] );
		$fsock_timeout = $timeout;
		if (($fsock = fsockopen ( $host_ip, 80, $errno, $errstr, $fsock_timeout )) < 0) {
			return false;
		}
	
		$needChar = false;
		if (is_array ( $post_data )) {
			foreach ( $post_data as $key => $val ) {
	
				$post_data2 .= ($needChar ? "&" : "") . urlencode ( $key ) . "=" . urlencode ( $val );
				$needChar = true;
			}
		} else {
			$post_data2 = $post_data;
		}
	
		$url2 ["query"]=$url2 ["query"] != "" ? $url2 ["query"].'&'.$post_data2:$post_data2;
	
		$request = $url2 ["path"] . ($url2 ["query"] != "" ? "?" . $url2 ["query"] : "") . ($url2 ["fragment"] != "" ? "#" . $url2 ["fragment"] : "");
		$in = "GET " . $request . " HTTP/1.0\r\n";
		$in .= "Accept: application/x-obml2d,*/*\r\n";
		$in .= "User-Agent: Payb-Agent\r\n";
		$in .= "Host: " . $url2 ["host"] . "\r\n";
		$in .= "Connection: Close\r\n\r\n";
	
		if (! @fwrite ( $fsock, $in, strlen ( $in ) )) {
			fclose ( $fsock );
			return false;
		}
		unset ( $in );
	
		$out = "";
		if ($return) {
			while ( $buff = @fgets ( $fsock, 2048 ) ) {
				$out .= $buff;
			}
				
			fclose ( $fsock );
				
			$pos = strpos ( $out, "\r\n\r\n" );
			$head = substr ( $out, 0, $pos ); // http head
			$status = substr ( $head, 0, strpos ( $head, "\r\n" ) ); // http status line
			$body = substr ( $out, $pos + 4, strlen ( $out ) - ($pos + 4) ); // page body
				
			if (preg_match ( "/^HTTP\/\d\.\d\s([\d]+)\s.*$/", $status, $matches )) {
				if (intval ( $matches [1] ) / 100 == 2) {
					return $body;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			fclose ( $fsock );
			return true;
		}
	}
	
	/* 远程请求异步方法 */
	public static function getSSCGET($url,$post_data, $return = false,$timeout=30) {
		

		$url2 = parse_url ( $url );
		
		$url2 ["path"] = ($url2 ["path"] == "" ? "/" : $url2 ["path"]);
		$url2 ["port"] = ($url2 ["port"] == "" ? 80 : $url2 ["port"]);
		$host_ip = @gethostbyname ( $url2 ["host"] );
		$fsock_timeout = $timeout;
		if (($fsock = fsockopen ( $host_ip, 80, $errno, $errstr, $fsock_timeout )) < 0) {
			return false;
		}
		
		$needChar = false;
		if (is_array ( $post_data )) {
			foreach ( $post_data as $key => $val ) {
		
				$post_data2 .= ($needChar ? "&" : "") . urlencode ( $key ) . "=" . urlencode ( $val );
				$needChar = true;
			}
		} else {
			$post_data2 = $post_data;
		}
		
		$url2 ["query"]=$url2 ["query"] != "" ? $url2 ["query"].'&'.$post_data2:$post_data2;
		
		$request = $url2 ["path"] . ($url2 ["query"] != "" ? "?" . $url2 ["query"] : "") . ($url2 ["fragment"] != "" ? "#" . $url2 ["fragment"] : "");
		$in = "GET " . $request . " HTTP/1.0\r\n";
		$in .= "Accept: */*\r\n";
		$in .= "User-Agent: Payb-Agent\r\n";
		$in .= "Host: " . $url2 ["host"] . "\r\n";
		$in .= "Connection: Close\r\n\r\n";
		
		if (! @fwrite ( $fsock, $in, strlen ( $in ) )) {
			fclose ( $fsock );
			return false;
		}
		unset ( $in );
		
		$out = "";
		if ($return) {
			while ( $buff = @fgets ( $fsock, 2048 ) ) {
				$out .= $buff;
			}
			
			fclose ( $fsock );
			
			$pos = strpos ( $out, "\r\n\r\n" );
			$head = substr ( $out, 0, $pos ); // http head
			$status = substr ( $head, 0, strpos ( $head, "\r\n" ) ); // http status line
			$body = substr ( $out, $pos + 4, strlen ( $out ) - ($pos + 4) ); // page body
			
			if (preg_match ( "/^HTTP\/\d\.\d\s([\d]+)\s.*$/", $status, $matches )) {
				if (intval ( $matches [1] ) / 100 == 2) {
					return $body;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			fclose ( $fsock );
			return true;
		}
	}
	public static function getSSCPOST($url, $post_data, $return = false,$timeout=30) {
		$url2 = parse_url ( $url );
		
		$url2 ["path"] = ($url2 ["path"] == "" ? "/" : $url2 ["path"]);
		$url2 ["port"] = ($url2 ["port"] == "" ? 80 : $url2 ["port"]);
		$host_ip = @gethostbyname ( $url2 ["host"] );
		
		$fsock_timeout = $timeout; // 秒
		if (($fsock = fsockopen ( $host_ip, 80, $errno, $errstr, $fsock_timeout )) < 0) {
			return false;
		}
		
		$request = $url2 ["path"] . ($url2 ["query"] != "" ? "?" . $url2 ["query"] : "") . ($url2 ["fragment"] != "" ? "#" . $url2 ["fragment"] : "");
		
		$needChar = false;
		if (is_array ( $post_data )) {
			foreach ( $post_data as $key => $val ) {
				
				$post_data2 .= ($needChar ? "&" : "") . urlencode ( $key ) . "=" . urlencode ( $val );
				$needChar = true;
			}
		} else {
			$post_data2 = $post_data;
		}
		
		$in = "POST " . $request . " HTTP/1.0\r\n";
		$in .= "Accept: */*\r\n";
		$in .= "Host: " . $url2 ["host"] . "\r\n";
		$in .= "User-Agent: Lowell-Agent\r\n";
		$in .= "Content-type: application/x-www-form-urlencoded\r\n";
		$in .= "Content-Length: " . strlen ( $post_data2 ) . "\r\n";
		$in .= "Connection: Close\r\n\r\n";
		$in .= $post_data2 . "\r\n\r\n";
		
		unset ( $post_data2 );
		if (! @fputs ( $fsock, $in, strlen ( $in ) )) {
			fclose ( $fsock );
			return false;
		}
		unset ( $in );
		
		$out = "";
		if ($return) {
			while ( $buff = fgets ( $fsock, 2048 ) ) {
				$out .= $buff;
			}
			
			fclose ( $fsock );
			$pos = strpos ( $out, "\r\n\r\n" );
			$head = substr ( $out, 0, $pos ); // http head
			$status = substr ( $head, 0, strpos ( $head, "\r\n" ) ); // http status line
			$body = substr ( $out, $pos + 4, strlen ( $out ) - ($pos + 4) ); // page body
			if (preg_match ( "/^HTTP\/\d\.\d\s([\d]+)\s.*$/", $status, $matches )) {
				if (intval ( $matches [1] ) / 100 == 2) {
					return $body;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			fclose ( $fsock );
			return true;
		}
	}
	
	/**
	 * 远程获取数据，POST模式
	 * 注意
	 * ：
	 * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
	 * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
	 * 
	 * @param $url 指定URL完整路径地址        	
	 * @param $cacert_url 指定当前工作目录绝对路径        	
	 * @param $para 请求的数据        	
	 * @param $input_charset 编码格式。默认值：空值
	 *        	return 远程输出的数据
	 */
	public static function getSSLPOST($url, $cacert_url, $para = array()) {
		$curl = curl_init ( $url );
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, true ); // SSL证书认证
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, 2 ); // 严格认证
		curl_setopt ( $curl, CURLOPT_CAINFO, $cacert_url ); // 证书地址
		curl_setopt ( $curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 ); // 显示输出结果
		curl_setopt ( $curl, CURLOPT_POST, true ); // post传输数据
		curl_setopt ( $curl, CURLOPT_POSTFIELDS, $para ); // post传输数据
		$responseText = curl_exec ( $curl );
		// var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
		curl_close ( $curl );
		
		return $responseText;
	}
	
	/**
	 * 远程获取数据，GET模式
	 * 注意：
	 * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
	 * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
	 * 
	 * @param $url 指定URL完整路径地址        	
	 * @param $cacert_url 指定当前工作目录绝对路径
	 *        	return 远程输出的数据
	 */
	public static function getSSLGET($url, $cacert_url, $para = array()) {
		if ($para) {
			foreach ( $para as $key => $value ) {
				if (strstr ( $url, "?" ))
					$url .= '&' . $key . '=' . urlencode ( $value );
				else
					$url .= '?' . $key . '=' . urlencode ( $value );
			}
		}
		
		$curl = curl_init ( $url );
		curl_setopt ( $curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 ); // 显示输出结果
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, true ); // SSL证书认证
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, 2 ); // 严格认证
		curl_setopt ( $curl, CURLOPT_CAINFO, $cacert_url ); // 证书地址
		$responseText = curl_exec ( $curl );
		// var_dump( curl_error($curl) );
		// 如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
		curl_close ( $curl );
		
		return $responseText;
	}
	public static function getPOST($url, $para = array(),$timeout=30) {
		
		// 初始化curl
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		// 设置header
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		// 要求结果为字符串且输出到屏幕上
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);   //只需要设置一个秒的数量就可以
		// post提交方式
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $para );
		
		// https请求 免验证
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		
		// 运行curl

		$responseText = curl_exec ( $ch );

		curl_close ( $ch );

		return $responseText;
	}
	
	
	public static function getGET($url, $para = array(),$timeout=30) {
		
		$url=HTTP::buildURL($url, $para);

		// 初始化curl
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		// 设置header
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		// 要求结果为字符串且输出到屏幕上
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);   //只需要设置一个秒的数量就可以
		// https请求 免验证
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		
		// 运行curl
		$responseText = curl_exec ( $ch );
		
		curl_close ( $ch );
		return $responseText;
	}

	public static function buildURL($url, $para = array()) {
	
		if ($para) {
			foreach ( $para as $key => $value ) {
				if (strstr ( $url, "?" ))
					$url .= '&' . $key . '=' . urlencode ( $value );
				else
					$url .= '?' . $key . '=' . urlencode ( $value );
			}
		}
		return $url;
	
	}
	public static function caculateAKSN($ak, $sk, $url, $querystring_arrays, $method = 'GET') {
		if ($method === 'POST') {
			ksort ( $querystring_arrays );
		}
		$querystring = http_build_query ( $querystring_arrays );
		
		return md5 ( urlencode ( $url . '?' . $querystring . $sk ) );
	}
	
	/**
	 * 构建通信sign
	 * 注意：
	 * 1.本算法兼容支付宝校验算法
	 * 
	 * @param $params 要校验的参数集合
	 * @param $signkey 加密秘钥
	 * @param $sign_method 校验加密方式 1 md5 2 sha1
	 * @param $sign_type  加密秘钥连接方式 1为数组尾缀 （系统算法|微信算法）2为字符尾缀（支付宝算法）
	 *        	return 校验sign的值
	 */
	
	public static function make_sign($params, $signkey, $sign_method = 1, $sign_type = 1) {
		if ($params ['sign_method'])
			$sign_method = $params ['sign_method'];
		if (is_array ( $params )) {
			// 对参数数组进行按key升序排列
			if (ksort ( $params )) {
				reset ( $params );
/*
				if ($sign_type == 1) {
					if (false === ($params ['key'] = $signkey)) {
						return false;
					}
					$arr_temp = array ();
					foreach ( $params as $k => $val ) {
						$arr_temp [] = $k . '=' . htmlspecialchars_decode($val);//url传输特殊字符还原
					}
					$sign_str =implode ( '&', $arr_temp );
				} else {
					$sign_str = HTTP::createLinkstring ( $params );
					$sign_str = $sign_str . trim ( $signkey );
				}
				*/
				$sign_str=HTTP::createLinkstring ( $params,$signkey,$sign_type );
				if(false === $sign_str) return false;

				// 选择相应的加密算法
				if ($sign_method == 1) {
					$sign = md5 ( $sign_str );
					return $sign;
				} else if ($sign_method == 2) {
					return sha1 ( $sign_str );
				} else {
					// $this->error['make_sign'] = '签名方法不支持';
					return false;
				}
			} else {
				// $this->error['make_sign'] = '表单参数数组排序失败';
				return false;
			}
		} else {
			// $this->error['make_sign'] = '生成签名的参数必须是一个数组';
			return false;
		}
	}
	public static function createLinkstring($para,$signkey,$sign_type = 1) {
		$arg = "";

		if($sign_type==1){
			if (false === ($para ['key'] = $signkey)) {
				return false;
			}
			$arr_temp = array ();
			foreach ( $para as $k => $val ) {
				$arr_temp [] = $k . '=' . htmlspecialchars_decode($val);//url传输特殊字符还原
			}
			$arg =implode ( '&', $arr_temp );
			
		}
		else
		{

		while ( list ( $key, $val ) = each ( $para ) ) {
			$arg .= $key . "=" . $val . "&";
		}
		// 去掉最后一个&字符
		$arg = substr ( $arg, 0, count ( $arg ) - 2 );
		
		// 如果存在转义字符，那么去掉转义
		if (get_magic_quotes_gpc ()) {
			$arg = stripslashes ( $arg );
		}
		$arg=$arg . trim ( $signkey );
		}
		
		
		return $arg;
	}
	/**
	 * 校验签名，传入的参数必须是一个数组，算法如下：
	 * 1.
	 * 删除数组中的签名sign元素
	 * 2. 对数组中的所有键值进行url反编码，避免传入的参数是经过url编码的
	 * 3. 利用商户密钥对新数组进行加密，生成签名
	 * 4. 比对生成签名和数组中原有的签名
	 *
	 * @param array $params        	
	 * @return boolean 失败返回false
	 */
	public static function check_sign($params, $key) {
		$sign = $params ['sign'];
		unset ( $params ['sign'] );
		foreach ( $params as &$value ) {
			$value =  rawurldecode($value); // URL编码的解码urldecode ()
		}
		unset ( $value );
		if (false !== ($my_sign = HTTP::make_sign ( $params, $key ))) {
			if (0 !== strcmp ( $my_sign, $sign )) {
				return false;
			}
			return true;
		} else {
			return false;
		}
	}
	
	public static function encodeHash($string,$key)
	{
		$pre_salt = substr(md5($string.$key  ), 16, 4);
	
		return base64_encode($string . ',' . $pre_salt);
	
	}
	public static function decodeHash($stringcode,$key)
	{
		$hash = base64_decode(trim($stringcode));
		$row = explode(',', $hash);
		if (count($row) != 2)
		{
			return false;
		}
		$string = $row[0];
		$salt = trim($row[1]);
	
		if (strlen($salt) != 4)
		{
			return false;
		}
	
		$pre_salt = substr(md5($string . $key ), 16, 4);
		if ($pre_salt == $salt)
		{
			return $string;
		}
		else
		{
			return false;
		}
	}
}
?>