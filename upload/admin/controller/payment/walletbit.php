<?php
class ControllerPaymentWalletBit extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('payment/walletbit');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('walletbit', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');

		$this->data['entry_test'] = $this->language->get('entry_test');
		$this->data['entry_email'] = $this->language->get('entry_email');
		$this->data['entry_token'] = $this->language->get('entry_token');
		$this->data['entry_securityword'] = $this->language->get('entry_securityword');
		$this->data['entry_exchangerate'] = $this->language->get('entry_exchangerate');
		$this->data['entry_exchangerate_text'] = $this->language->get('entry_exchangerate_text');

		$ticker = round(file_get_contents('https://walletbit.com/btcusd'), 2);
		$this->data['current_exchange_rate'] = $ticker;


		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$this->data['entry_ipn_text'] = $this->language->get('entry_ipn_text');
		$this->data['entry_ipn_url'] = str_replace('admin/', '', $this->url->link('payment/walletbit/callback')); // 'http://' . $_SERVER["HTTP_HOST"] . '/system/payment/ipn.php';

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['email'])) {
			$this->data['error_email'] = $this->error['email'];
		} else {
			$this->data['error_email'] = '';
		}

 		if (isset($this->error['token'])) {
			$this->data['error_token'] = $this->error['token'];
		} else {
			$this->data['error_token'] = '';
		}

 		if (isset($this->error['securityword'])) {
			$this->data['error_securityword'] = $this->error['securityword'];
		} else {
			$this->data['error_securityword'] = '';
		}

		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),      		
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/walletbit', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

		$this->data['action'] = $this->url->link('payment/walletbit', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['walletbit_test'])) {
			$this->data['walletbit_test'] = $this->request->post['walletbit_test'];
		} else {
			$this->data['walletbit_test'] = $this->config->get('walletbit_test');
		}

		if (isset($this->request->post['walletbit_email'])) {
			$this->data['walletbit_email'] = $this->request->post['walletbit_email'];
		} else {
			$this->data['walletbit_email'] = $this->config->get('walletbit_email');
		}

		if (isset($this->request->post['walletbit_token'])) {
			$this->data['walletbit_token'] = $this->request->post['walletbit_token'];
		} else {
			$this->data['walletbit_token'] = $this->config->get('walletbit_token');
		}

		if (isset($this->request->post['walletbit_securityword'])) {
			$this->data['walletbit_securityword'] = $this->request->post['walletbit_securityword'];
		} else {
			$this->data['walletbit_securityword'] = $this->config->get('walletbit_securityword');
		}

		if (isset($this->request->post['walletbit_exchangerate'])) {
			$this->data['walletbit_exchangerate'] = $this->request->post['walletbit_exchangerate'];
		} else {
			$this->data['walletbit_exchangerate'] = $this->config->get('walletbit_exchangerate');
		}

		$this->load->model('localisation/order_status');

		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['walletbit_status'])) {
			$this->data['walletbit_status'] = $this->request->post['walletbit_status'];
		} else {
			$this->data['walletbit_status'] = $this->config->get('walletbit_status');
		}
		
		if (isset($this->request->post['walletbit_sort_order'])) {
			$this->data['walletbit_sort_order'] = $this->request->post['walletbit_sort_order'];
		} else {
			$this->data['walletbit_sort_order'] = $this->config->get('walletbit_sort_order');
		}

		$this->template = 'payment/walletbit.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/walletbit')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['walletbit_email']) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if (!$this->request->post['walletbit_token']) {
			$this->error['token'] = $this->language->get('error_token');
		}

		if (!$this->request->post['walletbit_securityword']) {
			$this->error['securityword'] = $this->language->get('error_securityword');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>