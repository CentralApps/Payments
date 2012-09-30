<?php
namespace CentralApps\Payments;

interface OrderFactoryInterface {
	
	public function __construct(\PDO $database_engine );
	
	public function createFromOrderId($order_id);
	
	public function getByTransactionReference($transaction_reference);
	
}
