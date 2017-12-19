<?php
class runtime
{
	private  $startTime = 0;
	private  $stopTime = 0;
	private  $costTime=0;
	private  $request;
    private  $route;
    private  $timearray;
	public function __construct() {
		//$this->request = $registry->get('request');	
		if(!isset($_REQUEST['route']))
			$this->route='';
		else 
			$this->route=$_REQUEST['route'];
				
		    $this->costTime = $this->get_microtime();
		    $this->timearray=array();	
	}
	
	private function get_microtime()
	{
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}

	public function start()
	{
		$this->startTime = $this->get_microtime();
		$this->costTime = $this->get_microtime();
	}

	public function stop()
	{
		$this->stopTime = $this->get_microtime();
	}
	public function cost($tag){
		
		$costtime=$this->route.' | '.$tag.' | '.round(($this->get_microtime() - $this->costTime) * 1000, 1).'ms,';
		$this->costTime=$this->get_microtime();
		return $costtime;
		
	}
    public function time($tag='')
    {
    	return $this->route.' | '.$tag.' | '.round(($this->get_microtime() - $this->startTime) * 1000, 1).'ms,';
    	
    }
    
	public function spent($tag='')
	{
		return $this->route.' | '.$tag.' | '.round(($this->stopTime - $this->startTime) * 1000, 1).'ms,';
	}

}