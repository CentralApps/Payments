<?php
namespace CentralApps\GoCardlessPaymentsProcessor;

class Callback extends \CentralApps\Payments\AbstractCallback {
	
	protected $orderFactory;
	
	public function __construct(\CentralApps\Payments\OrderFactoryInterface $order_factory)
	{
		$this->orderFactory = $order_factory;
	}
	
	public function processCallback()
	{
		$webhook = file_get_contents('php://input');
		
		$webhook_array = json_decode($webhook, true);
		
		if( \GoCardless::validate_webhook($webhook_array['payload']) == true ) {
			header('HTTP/1.1 200 OK');
			
			foreach($webhook_array['payload']['bills'] as $bill) {
				$orders = $this->orderFactory->getByTransactionReference($bill['id']);
				if(count($orders) == 1) {
					$order = $orders->pop();
					if(floatval($order->getTotalCost()) == floatval($bill['amount'])) {
						$this->updateOrder($order, $bill['status']);
					}
				}
			}
		}
	}

	private function updateOrder($order, $status)
	{
		switch($bill['status']) {
          	case 'created':
              	$new_status = 'processing';
              	break;
          	case 'paid':
             	$new_status = 'paid';
              	break;
          	case 'failed':
              	$new_status = 'cancelled';
              	break;
          	case 'refunded':
              	$new_status = 'refunded';
            	break;
          	default:
              	$new_status = null;
              	break;
    	}
		
		if( ! is_null($new_status) ) {
			$this->callback($order, $new_status);
		}
	}
	
	
}
