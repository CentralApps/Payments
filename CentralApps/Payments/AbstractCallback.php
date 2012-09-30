<?php
namespace CentralApps\Payments;

abstract class AbstractCallback {
	
	protected $callbacks = array();
	
	public function __construct()
	{
		
	}
	
	public function setCallback(\Closure $callback)
	{
		$this->callback = $callback;
	}
	
	
}
