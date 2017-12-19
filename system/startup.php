<?php
// Error Reporting

if (phpversion() > '5.5'){
	error_reporting(E_ALL ^ E_DEPRECATED);
}else {
	//error_reporting(E_ALL) ;
	error_reporting(E_ALL ^ E_NOTICE);
}

// Check Version
if (version_compare(phpversion(), '5.1.0', '<') == TRUE) {
	exit('PHP5.1+ Required');
}

// Register Globals
if (ini_get('register_globals')) {
	ini_set('session.use_cookies', 'On');
	ini_set('session.use_trans_sid', 'Off');
	// added this to set tmp for web hosting.
	define('ROOT_PATH', str_replace("\\", '/', dirname(__FILE__)));
	$savePath=ROOT_PATH."/tmp";
	
	session_save_path($savePath);
	session_set_cookie_params(0, '/');
	session_start();
	
	$globals = array($_REQUEST, $_SESSION, $_SERVER, $_FILES);

	foreach ($globals as $global) {
		foreach(array_keys($global) as $key) {
			unset($$key);
		}
	}
}


// Magic Quotes Fix
if (ini_get('magic_quotes_gpc')) {
	function clean($data) {
   		if (is_array($data)) {
  			foreach ($data as $key => $value) {
    			$data[clean($key)] = clean($value);
  			}
		} else {
  			$data = stripslashes($data);
		}
	
		return $data;
	}			
	
	$_GET = clean($_GET);
	$_POST = clean($_POST);
	$_REQUEST = clean($_REQUEST);
	$_COOKIE = clean($_COOKIE);
}

//if (!ini_get('date.timezone')) {
	date_default_timezone_set('Asia/Shanghai');
//	date_default_timezone_set('UTC');
//}

// Windows IIS Compatibility  
if (!isset($_SERVER['DOCUMENT_ROOT'])) { 
	if (isset($_SERVER['SCRIPT_FILENAME'])) {
		$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['PHP_SELF'])));
	}
}

if (!isset($_SERVER['DOCUMENT_ROOT'])) {
	if (isset($_SERVER['PATH_TRANSLATED'])) {
		$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF'])));
	}
}

if (!isset($_SERVER['REQUEST_URI'])) { 
	$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 1); 
	
	if (isset($_SERVER['QUERY_STRING'])) { 
		$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING']; 
	} 
}
if(defined('WEB_SCAN')&&WEB_SCAN)
require_once(DIR_SYSTEM .'360_safe/360webscan.php'); // 注意文件路径
// Helper
require_once(DIR_SYSTEM . 'helper/utf8.php'); 

require_once(DIR_SYSTEM . 'helper/common.php'); 

require_once(DIR_SYSTEM . 'helper/functions.php'); 

require_once(DIR_SYSTEM . 'helper/DBHelper.php');
//echo(DIR_SYSTEM.'1');die();
require_once(DIR_SYSTEM . 'helper/ReqHelper.php');
require_once(DIR_SYSTEM . 'helper/ArrayHelper.php');
require_once(DIR_SYSTEM . 'helper/lbsHelp.php');

// Engine

require_once(DIR_SYSTEM . 'engine/action.php'); 
require_once(DIR_SYSTEM . 'engine/controller.php');
require_once(DIR_SYSTEM . 'engine/front.php');
require_once(DIR_SYSTEM . 'engine/loader.php'); 
require_once(DIR_SYSTEM . 'engine/model.php');
require_once(DIR_SYSTEM . 'engine/service.php');
require_once(DIR_SYSTEM . 'engine/registry.php');
// Common
require_once(DIR_SYSTEM . 'library/cache.php');
if(class_exists('Memcache'))
require_once(DIR_SYSTEM . 'library/mcache.php');

require_once(DIR_SYSTEM . 'library/url.php');
require_once(DIR_SYSTEM . 'library/config.php');
require_once(DIR_SYSTEM . 'library/db.php');
require_once(DIR_SYSTEM . 'library/document.php');
require_once(DIR_SYSTEM . 'library/image.php');
require_once(DIR_SYSTEM . 'library/language.php');
require_once(DIR_SYSTEM . 'library/log.php');
require_once(DIR_SYSTEM . 'log4php/Logger.php');
require_once(DIR_SYSTEM . 'library/mail.php');
require_once(DIR_SYSTEM . 'library/pagination.php');
require_once(DIR_SYSTEM . 'library/request.php');
require_once(DIR_SYSTEM . 'library/response.php');
require_once(DIR_SYSTEM . 'library/session.php');
require_once(DIR_SYSTEM . 'library/template.php');
require_once(DIR_SYSTEM . 'library/sitemap.php');
require_once(DIR_SYSTEM . 'library/common.php');
require_once(DIR_SYSTEM . 'library/seo.php');
require_once(DIR_SYSTEM . 'library/rewrite.php');
require_once(DIR_SYSTEM . 'library/runtime.php');
require_once(DIR_SYSTEM . 'library/encryption.php');
require_once(DIR_SYSTEM . 'library/sms.php');
require_once(DIR_SYSTEM . 'library/http.php');
require_once(DIR_SYSTEM . 'helper/mobile.php');

// Forward to mobile or 
require_once(DIR_SYSTEM . 'library/Mobile_Detect.php');

// Config URL route
// TODO this only using to rewrite URL base rules.
require_once(DIR_SYSTEM.'config/routes.php');

// Data
require_once(DIR_SYSTEM . 'data/magic_variable.php'); 

// Utility
require_once(DIR_SYSTEM . 'utility/maths.php');

require_once(DIR_SYSTEM . 'library/oss.php');
?>