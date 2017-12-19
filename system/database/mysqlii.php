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

final class MySQLii extends mysqli {
	private $requery=0;
	private $maxtry=5;
	public function __construct($hostname, $username, $password, $database) {
	
	         parent::mysqli($hostname, $username, $password, $database);

		
    	parent::set_charset("utf8");
    	parent::query("SET GLOBAL sql_mode = ''");
    	
    	/*
		mysql_query("SET NAMES 'utf8'", $this->connection);
		mysql_query("SET CHARACTER SET utf8", $this->connection);
		mysql_query("SET CHARACTER_SET_CONNECTION=utf8", $this->connection);
		mysql_query("SET SQL_MODE = ''", $this->connection);
		*/
  	}
  	
  	public function multi_query($sql){
  		if (parent::multi_query($sql)) {
  			do {
  				/* store first result set */
  				if ($result = parent::store_result()) {
  					
  					while ($data = $result->fetch_all(MYSQLI_ASSOC)) {
  						
  						$query = new stdClass();
  						{
  							$query->row = isset($data[0]) ? $data[0] : array();
  							$query->rows = $data;
  							$query->num_rows = $resource->num_rows;
  						}
  						$rows[]=$query;
  					}
  					
  				
  					$result->free();
  				}
  				/* print divider */
  				if (parent::more_results()) {
  					//printf("-----------------\n");
  				}else{break;}
  			} while (parent::next_result());
  		}
  		return $rows;
  	}

  	public function query($sql) {

  				$resource = parent::query($sql);

		if ($resource) {
			if($resource!==true){
				$i = 0;  	
				$data = array();	
				/*
				while ($result = $resource->fetch_assoc()) {

					$data[$i] = $result;   	
					$i++;
				}	
				*/
				$data = $resource->fetch_all(MYSQLI_ASSOC);
                			
				$query = new stdClass();
				{
				$query->row = isset($data[0]) ? $data[0] : array();
				$query->rows = $data;
				$query->num_rows = $resource->num_rows;
				}
				//else
				{
				//	$query=$resource;
				}
				$resource->free();
				unset($data);
				return $query;	
			}
			else {
				return true;
			}
		} else {
			if($this->errno){
				//throw
				//new Exception("MySQL Error No: $this->errno <br> error $mysqli->error <br> Query:<br> $sql", E_USER_WARNING);
				error_handler(E_ERROR,'MySQLi Error: ' .  ($this->error) . '<br />Error No: ' .  ($this->errno) . '<br />' . $sql, '', $this->errno);		
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
					
					error_handler(E_ERROR,'Error: ' .  ($this->error) . '<br />Error No: maxtry<br />' . $sql, '', $this->errno);
					
				}
			}
    	}
    	return false;
  	}
	
	public function escape($value) {
		return parent::real_escape_string($value);
	}
	
  	public function countAffected() {
    	return $this->affected_rows;
  	}

  	public function getLastId() {
    	return $this->insert_id;
  	}	
	
	public function __destruct() {
		//if(parent)
		//parent::close();
	}
}
?>