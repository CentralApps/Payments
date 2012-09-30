<?php
namespace CentralApps\Payments;

interface OrderInterface {
	
	public function setStatus($status);
	
	public function setPaid($paid);
	
	public function activatePurchases();
	
	public function deactivatePurchases();
	
	public function getTotalCost();
	
	public function setUpdated($date);
	
	public function save();
	
}
