<?php
final class Registry {
	private $data = array();

	public function get($key) {
		return (isset($this->data[$key]) ? $this->data[$key] : NULL);
	}

	public function set($key, &$value) {
		$this->data[$key] = $value;
	}
	public function __get($key) {
		return  $this->get($key);
	}

	public function has($key) {
    	return isset($this->data[$key]);
  	}
}
?>