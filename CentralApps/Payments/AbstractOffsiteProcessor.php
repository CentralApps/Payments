<?php
namespace CentralApps\Payments;

abstract class AbstractOffsiteProcessor {
	
	public function processSuccessfulReturn();
	
	public function processCancelReturn();
	
	public function processCallback();
	
}
