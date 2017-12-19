<?php

class Maths {
    
    /**
     * 生成指定长度随机字符串
     * @param unknown $length
     * @param string $prefix
     * @return boolean|string
     */
    public static function genRandomCode($length, $prefix = null) {
        $len_gen = $length;
        
        if( $prefix){
            $len_pre = strlen($prefix);
            
            $len_gen = $length - $len_pre;
            
            if( $len_gen <= 0 ){
                return false;
            }
        }
            
        $chars_array = array(
            "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z",
        );
        $charsLen = count($chars_array) - 1;
        
        $outputstr = $prefix;
        for ($i=0; $i<$len_gen; $i++)
        {
            $outputstr .= $chars_array[mt_rand(0, $charsLen)];
        }
        
        return $outputstr;
        
    }
	
	/**
	 * 随机生成码 可去重
	 * @param type $length
	 * @param type $prefix
	 * @return type
	 */
	public function create_code($arr = array(), $length, $prefix = '',$num = 1){
		$str = '123456789qwertyuiopasdfghjklzxcvbnm';
		$length = $length - strlen($prefix);
		for($i = 0; $i < $num; $i++){
			$code = strtoupper(substr(str_shuffle($str), 0, $length));
			$data[] = $prefix.$code;
		}
		
		$data = array_unique($data);
		$data = array_merge($arr, $data);
		$real_num = count($data);
		if($real_num < $num){
			$data = self::create_code($data, $length, $prefix, $num - $real_num);
		}
		return $data;
	}
}
?>