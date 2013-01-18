<?php
namespace CentralApps\Payments\Providers\PayPalIPN;

use CentralApps\Payments\AbstractCallback;
use CentralApps\Payments\OrderFactoryInterface;

class Callback extends AbstractCallback
{
    protected $orderFactory;
    
    public function __construct(OrderFactoryInterface $orderFactory)
    {
        $this->orderFactory = $orderFactory;
    }
    
    public function processCallback()
    {
        parse_str(file_get_contents('php://input'), $post_data);
        $request = array(
            'cmd' => '_notify-validate'
        );
        foreach ($post_data as $key => $value) {
            if (get_magic_quotes_gpc()) {
                $value = stripslashes($value);
            }
            $request[$key] = urlencode($value);
        }
        $request = http_build_query($request);
        
        $ch = curl_init('https://www.paypal.com/cgi-bin/webscr');
        curl_setopt_array($ch, array(
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => array('Connection: Close'),
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $request,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
        ));
        
        if (false === ($response = curl_exec($ch))) {
            curl_close($ch);
            exit;
            // throw Exception?
        }
        curl_close($ch);
        
        if (strcmp($response, 'VERIFIED') == 0) {
            $orders = $this->orderFactory->getByTransactionReference($post_data['txn_id']);
            if (count($orders) == 1) {
                $order = $orders->pop();
                if (($post_data['payment_status'] == 'Completed') && (floatval($order->getTotalCost()) === floatval($post_data['payment_amount']))) {
                    $this->callback($order, 'paid');
                    return;
                }
            }
        }
        
        $this->callback($order, 'cancelled');
    }
}