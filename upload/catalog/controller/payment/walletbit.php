<?php
class ControllerPaymentWalletBit extends Controller {
	protected function index() {
		$this->language->load('payment/walletbit');
		
		$this->data['text_testmode'] = $this->language->get('text_testmode');

		$this->data['testmode'] = $this->config->get('walletbit_test');
		
		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if ($order_info)
		{
			$currency = $order_info['currency_code'];

			$this->data['token'] = $this->config->get('walletbit_token');
			
			$titles = '';
			
			foreach ($this->cart->getProducts() as $product)
			{
				$titles .= $product['name'] . ', ';
			}

			$titles = substr($titles, 0, -2);

			$this->data['discount_amount_cart'] = 0;

			$total = $this->currency->format($order_info['total'] - $this->cart->getSubTotal(), $currency, false, false);

			if ($total > 0) {
				$this->data['products'][] = array(
					'name'     => $this->language->get('text_total'),
					'model'    => '',
					'price'    => $total,
					'quantity' => 1,
					'option'   => array(),
					'weight'   => 0
				);	
			} else {
				$this->data['discount_amount_cart'] -= $this->currency->format($total, $currency, FALSE, FALSE);
			}

			$this->data['total'] = 0;

			if (strtolower($currency) == 'btc')
			{
				$this->data['total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
			}
			else
			{
				$exchange_rate_dollars = $this->config->get('walletbit_exchangerate');

				$ticker = floor(file_get_contents('https://walletbit.com/btcusd'));

				if ($ticker < $exchange_rate_dollars)
				{
					$ticker = $exchange_rate_dollars;
				}

				$price = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);

				if ($currency != 'USD')
				{
					$ch = curl_init();
					$timeout = 0;
					curl_setopt ($ch, CURLOPT_URL, 'http://www.google.com/ig/calculator?hl=en&q=' . $price . $currency . '=?USD');
					curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch,  CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
					curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
					$rawdata = curl_exec($ch);
					curl_close($ch);
	
					$data = explode('"', $rawdata);
					$data = explode(' ', $data['3']);
					$var = $data['0'];

					$new_string = preg_replace("/[^0-9,.]/", "", $var);

					$price = round($new_string, 3);
				}

				$this->data['total'] = round($price / $ticker, 8);
			}

			$this->data['item_name'] = $titles;
			$this->data['return'] = $this->url->link('checkout/success');

			$this->load->library('encryption');

			$encryption = new Encryption($this->config->get('config_encryption'));
	
			$this->data['custom'] = 'custom=' . rawurldecode($encryption->encrypt($this->session->data['order_id'])) . '|b=' . rawurlencode($encryption->encrypt($this->data['total']));
		
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/walletbit.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/payment/walletbit.tpl';
			} else {
				$this->template = 'default/template/payment/walletbit.tpl';
			}
	
			$this->render();
		}
	}
	
	public function callback() {
		$this->load->library('encryption');
	
		$encryption = new Encryption($this->config->get('config_encryption'));
		
		if (isset($this->request->post['custom'])) {
			$order_id = $encryption->decrypt(rawurldecode($this->request->post['custom']));
		} else {
			$order_id = 0;
		}

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($order_id);
		
		if ($order_info)
		{
			$str =
			$_POST["merchant"].":".
			$_POST["customer_email"].":".
			$_POST["amount"].":".
			$_POST["batchnumber"].":".
			$_POST["txid"].":".
			$_POST["address"].":".
			$this->config->get('walletbit_securityword');

			$hash = strtoupper(hash('sha256', $str));

			// proccessing payment only if hash is valid
			if ($_POST["merchant"] == $this->config->get('walletbit_email') && $_POST["encrypted"] == $hash && $_POST["status"] == 1)
			{
				print '1';

				$order_status_id = $this->config->get('config_order_status_id');

				if ((float)$this->request->post['amount'] == (float)$encryption->decrypt(rawurldecode($this->request->post['b'])))
				{
					$order_status_id = 5;
				}

				if (!$order_info['order_status_id']) {
					$this->model_checkout_order->confirm($order_id, $order_status_id);
				} else {
					$this->model_checkout_order->update($order_id, $order_status_id);
				}
			}
			else
			{
				print "Incorrect IPN";
				$this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'));
			}
		}
	}
}
?>