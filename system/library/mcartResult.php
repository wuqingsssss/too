<?php
class mCartResult {
	const STATUS_FAIL = 'fail';
    const STATUS_SUCCESS = 'success';
    const ERROR_SYSTEM_INVALID_API = '0x0001';
    const ERROR_SYSTEM_INVALID_SESSION_KEY = '0x0002';
    const ERROR_SYSTEM_TIME_OVER_TEM_MIN = '0x0003';
    const ERROR_SYSTEM_INVALID_RESPONSE_FORMAT = '0x0004';
    const ERROR_SYSTEM_INVALID_API_VERSION = '0x0005';
    const ERROR_SYSTEM_INVALID_ENCRYPTION_METHOD = '0x0006';
    const ERROR_SYTEM_LANAGE_IS_NOT_SUPPORTED = '0x0007';
    const ERROR_SYSTEM_CURRENCY_IS_NOT_SUPPORTED = '0x0008';
    const ERROR_SYSTEM_AUTHENTICATION_FAILED = '0x0009';
    const ERROR_SYSTEM_TIME_OUT = '0x0010';
    const ERROR_SYSTEM_DATA = '0x0011';
    const ERROR_SYSTEM_DATABASE = '0x0012';
    const ERROR_SYSETM_SERVER = '0x0013';
    const ERROR_SYSTEM_PERMISSION_DENIED = '0x0014';
    const ERROR_SYSTEM_SERVICE_UNAVAILABLE = '0x0015';
    const ERROR_SYSTEM_INVALID_SIGNATURE = '0x0016';
    const ERROR_SYSTEM_INVALID_SESSION_ID = '0x0017';
    const ERROR_SYSTEM_INVALID_METHOD = '0x0018';
    const ERROR_UNKNOWN_ERROR = '0xFFFF';
    const ERROR_NO_RECORD = '0xFFF0';
    const ERROR_INPUT_PARAMETER = '0xFFF1';

    static $errorDesc = array(
        self::ERROR_SYSTEM_INVALID_API => 'Invalid API (System)',
        self::ERROR_SYSTEM_INVALID_SESSION_KEY => 'Invalid SessionKey (System)',
        self::ERROR_SYSTEM_TIME_OVER_TEM_MIN => 'Time error over 10min (System)',
        self::ERROR_SYSTEM_INVALID_RESPONSE_FORMAT => 'Invalid response format (System)',
        self::ERROR_SYSTEM_INVALID_API_VERSION => 'Invalid API version (System)',
        self::ERROR_SYSTEM_INVALID_ENCRYPTION_METHOD => 'Invalid encryption method (System)',
        self::ERROR_SYTEM_LANAGE_IS_NOT_SUPPORTED => 'Language is not supported (System)',
        self::ERROR_SYSTEM_CURRENCY_IS_NOT_SUPPORTED => 'Currency is not supported (System)',
        self::ERROR_SYSTEM_AUTHENTICATION_FAILED => 'Authentication failed (System)',
        self::ERROR_SYSTEM_TIME_OUT => 'Time out (System)',
        self::ERROR_SYSTEM_DATA => 'Data error (System)',
        self::ERROR_SYSTEM_DATABASE => 'DataBase error (System)',
        self::ERROR_SYSETM_SERVER => 'Server error (System)',
        self::ERROR_SYSTEM_PERMISSION_DENIED => 'Permission denied (System)',
        self::ERROR_SYSTEM_SERVICE_UNAVAILABLE => 'Service unavailable (System)',
        self::ERROR_SYSTEM_INVALID_SIGNATURE => 'Invalid signature (System)',
        self::ERROR_SYSTEM_INVALID_SESSION_ID => 'Invalid session ID (System)',
        self::ERROR_SYSTEM_INVALID_METHOD => 'Invalid method (System)',
        self::ERROR_UNKNOWN_ERROR => 'Unknown error .',
   		self::ERROR_NO_RECORD => 'No Record found.',
   		self::ERROR_INPUT_PARAMETER => 'Parameter missing or error',
    );

  
	protected $errMsg;
    protected $code;  // error code 
    protected $flag; // 0 as success,1 as error
    protected $results; 
    protected $total; // would be used when retur list
    protected $fields;

    public function setSuccess($info = null, $fields = null,$total='') {
        $this->result = self::STATUS_SUCCESS;
        $this->errMsg = '';
        $this->flag = '0';
        $this->code = '0x0000';
        $this->results = $info;
        $this->fields = $fields;
        $this->total = $total;
    }
    
    public function setError($code, $msg = null, $redirect_to_page = false) {
        $this->result = self::STATUS_FAIL;
        $this->code = $code;
        $this->results = array();
        $this->flag = '1';
        $this->total = '0';
        if (is_null($msg)) {
            self::$errorDesc[$code];
            $this->errMsg = self::$errorDesc[$code];
        } else {
            $this->errMsg = $msg;
        }
       
		if ($redirect_to_page) {
            $this->results['redirect_to_page'] = $redirect_to_page;
        }
    }

    public function returnResult() {
        return array('errMsg' => $this->errMsg, 'code' => $this->code, 'flag' => $this->flag, 'totalItems' => $this->total, 'result' => $this->results);
    }

}

?>
