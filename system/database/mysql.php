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

final class MySQL {
	private $connection;
	private $requery=0;
	private $maxtry=5;
	public function __construct($hostname, $username, $password, $database) {
		if (!$this->connection = mysql_connect($hostname, $username, $password)) {
      		exit('Error: Could not make a database connection using ' . $username . '@' . $hostname);
    	}

    	if (!mysql_select_db($database, $this->connection)) {
      		exit('Error: Could not connect to database ' . $database);
    	}
		
		mysql_query("SET NAMES 'utf8'", $this->connection);
		mysql_query("SET CHARACTER SET utf8", $this->connection);
		mysql_query("SET CHARACTER_SET_CONNECTION=utf8", $this->connection);
		mysql_query("SET SQL_MODE = ''", $this->connection);
  	}
		
  	public function multi_query($sqls){
  		$sqla=explode(';', trim($sqls,';'));
  		 foreach($sqla as $sql)
  			      $rows[]= $this->query($sql); 	
  		 return $rows;	
  	}
  	
  	public function query($sql) {
  		$resource = mysql_query($sql, $this->connection);
  		

		if ($resource) {
			if (is_resource($resource)) {
				$i = 0;
    	
				$data = array();
		
				while ($result = mysql_fetch_assoc($resource)) {
					$data[$i] = $result;
    	
					$i++;
				}
				
				mysql_free_result($resource);
				
				$query = new stdClass();
				$query->row = isset($data[0]) ? $data[0] : array();
				$query->rows = $data;
				$query->num_rows = $i;
				
				unset($data);
				return $query;	
    		} else {
				return TRUE;
			}
		} else {
			//exit('Error: ' . mysql_error($this->connection) . '<br />Error No: ' . mysql_errno($this->connection) . '<br />' . $sql);
			
			if($this->errno){
			error_handler(E_ERROR,'Error: ' .  ($this->error) . '<br />Error No: ' .  ($this->errno) . '<br />' . $sql, '', $this->errno);
			}
		  else
			{
			
				$this->requery++;
				if($this->requery<=$this->maxtry)
				{
					 sleep(1);
					//usleep(1000);
					$this->query($sql);	
				}
				else 
				{
					
					error_handler(E_ERROR,'MySQL Error: ' .  ($this->error) . '<br />Error No: maxtry<br />' . $sql, '', $this->errno);
					
				}
			}
    	}
    	return false;
  	}
	
	public function escape($value) {
		return mysql_real_escape_string($value, $this->connection);
	}
	
  	public function countAffected() {
    	return mysql_affected_rows($this->connection);
  	}

  	public function getLastId() {
    	return mysql_insert_id($this->connection);
  	}	
	
	public function __destruct() {
		mysql_close($this->connection);
	}
}
?>