<?php 
class ModelPaymentWalletBit extends Model {
  	public function getMethod($address, $total) {
		$this->load->language('payment/walletbit');

		$status = true;

		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'       => 'walletbit',
        		'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('walletbit_sort_order')
      		);
    	}
   
    	return $method_data;
  	}
}
?>