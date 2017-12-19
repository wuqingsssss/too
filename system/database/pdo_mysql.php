<?php
// Error Reporting
//error_reporting(E_ALL ^ E_DEPRECATED);
//error_reporting(E_ALL);

/*
if (phpversion() > '5.5'){
	error_reporting(E_ALL ^ E_DEPRECATED);
}else {
	error_reporting(E_ALL) ;
	//error_reporting(0);
}*/

final class PDO_MySQL extends PDO {
	public function __construct($hostname, $username, $password, $database) {
		                $dsn = 'mysql:host='.$hostname.';dbname='.$database;
	                 	$username = $username;
	                 	$password = $password;
	                 	$options = array(
			       	parent::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',);
          	
	           parent::__construct($dsn, $username, $password, $options);
	           parent::query("SET GLOBAL sql_mode = ''");

  	}

  	public function query($sql) {

  		$resource = parent::query($sql);
  		
		if ($resource) {
			if($resource!==true){
				$i = 0;  	
				$data = array();	
		
				$data = $resource->fetchAll($this->FETCH_ASSOC);
                			
				$query = new stdClass();
				{
				$query->row = isset($data[0]) ? $data[0] : array();
				$query->rows = $data;
				$query->num_rows = $resource->rowCount();
				}
				//else
				{
				//	$query=$resource;
				}
				$resource->closeCursor();
				unset($data);
				return $query;	
			}
			else {
				return true;
			}
		} else {
			exit('Error: ' .  ($this->error) . '<br />Error No: ' .  ($this->errno) . '<br />' . $sql);
    	}
  	}
	
	public function escape($value) {
		return $value;
	}
	
  	public function countAffected() {
    	return $this->affected_rows;
  	}

  	public function getLastId() {
    	return parent::lastInsertId();
  	}	
	
	public function __destruct() {
		//parent::close();
	}
}
?>