<?php
final class DB {
	private $driver;
	private $drivername;
	private $drivername2 = false;
	private $driver2 = false;
	public function __construct($driver, $driver2 = array()) {
		if (file_exists ( DIR_DATABASE . $driver ['driver'] . '.php' )) {
			require_once (DIR_DATABASE . $driver ['driver'] . '.php');
		} else {
			exit ( 'Error: Could not load database file ' . $driver ['driver'] . '!' );
		}
		$this->drivername = $driver ['driver'];
		$this->driver = new $driver ['driver'] ( $driver ['hostname'], $driver ['username'], $driver ['password'], $driver ['database'] );
		if ($driver2) {
			if (file_exists ( DIR_DATABASE . $driver2 ['driver'] . '.php' )) {
				require_once (DIR_DATABASE . $driver2 ['driver'] . '.php');
			} else {
				exit ( 'Error: Could not load database file ' . $driver ['driver'] . '!' );
			}
			$this->drivername2 = $driver2 ['driver'];
			$this->driver2 = new $driver ['driver'] ( $driver2 ['hostname'], $driver2 ['username'], $driver2 ['password'], $driver2 ['database'] );
		}
	}
	
	public function multi_query($sql){
		
		$res = $this->driver->multi_query( $sql );
		$sql = $this->drivername . '::multi_query::'. $sql;
	
		$log_db = getRegistry ()->get ( 'log_db' );
		if ($log_db) {
				$log_db->info ( $sql );		
		}
		return $res;
	}
	public function query($sql, $cache = true) {
		if (stripos ( $sql, 'select' ) !== false) {
			
			$config_cache = defined ( 'DB_SELECT_CACHE' ) ? DB_SELECT_CACHE : $cache;
			$config_cache_time = defined ( 'DB_SELECT_CACHE_TIME' ) ? DB_SELECT_CACHE_TIME : 5;
			$mem = getRegistry ()->get ( 'mem' );
			if ($mem && $cache && $config_cache) {
				$key = 'select' . md5 ( $sql );
				$tt = $mem->get ( $key );
				if (! $tt) {
					if ($this->driver2) {
						$res = $this->driver2->query ( $sql );
						$sql = $this->drivername2 . ':' . $sql;
					} else {
						$res = $this->driver->query ( $sql );
						$sql = $this->drivername . ':' . $sql;
					}
					
					$mem->set ( $key, $res, 0, $config_cache_time );
				} else {
					$res = $tt;
					$sql = 'memcache:key:' . $key . ':' . $sql;
					if ($this->driver2) {
						$sql = $this->drivername2 . ':' . $sql;
					} else {
						$sql = $this->drivername . ':' . $sql;
					}
				}
			} else {
				
				if ($this->driver2) {
					$res = $this->driver2->query ( $sql );
					$sql = $this->drivername2 . ':' . $sql;
				} else {
					$res = $this->driver->query ( $sql );
					$sql = $this->drivername . ':' . $sql;
				}
			}
		} else {
			$res = $this->driver->query ( $sql );
			$sql = $this->drivername . ':' . $sql;
		}
		$log_db = getRegistry ()->get ( 'log_db' );
		if ($log_db) {
			
			if (stripos ( $sql, 'select' ) !== false) {
				$log_db->debug ( $sql );
			} elseif (stripos ( $sql, 'insert' ) !== false) {
				$log_db->info ( $sql );
			} elseif (stripos ( $sql, 'delete' ) !== false) {
				$log_db->info ( $sql );
			} elseif (stripos ( $sql, 'update' ) !== false) {
				$log_db->info ( $sql );
			} else {
				$log_db->warn ( $sql );
			}
		}
		
		return $res;
	}
	public function escape($value) {
		return $this->driver->escape ( $value );
	}
	public function countAffected() {
		return $this->driver->countAffected ();
	}
	public function clean_nonchar($str) {//匹配汉字字符和制定字符
		if (preg_match_all ( '/[a-zA-Z0-9\x{4e00}-\x{9fa5}\s\-\[\]\)\(]+/u', $str, $m )) {
			return implode ( $m [0] );
		} else
			return '';
	}
	public function match_phone($str) {
		if (preg_match_all ( '/(13|14|15|17|18)[0-9]{9}/', $str, $m )) {
			return implode ( $m [0] );
		} else
			return '';
	}
	public function getLastId() {
		return $this->driver->getLastId ();
	}
}
?>