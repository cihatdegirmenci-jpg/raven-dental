<?php

function dump($x)
{
	echo "<pre>";
	print_r($x);
	echo "</pre>";
}

class ControllerExtensionPaymentqnbpay extends Controller
{
	private $error = array();

	public function recurringCancel()
	{
		return "";
	}

	function generateRandomString($length = 10)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public function index()
	{
		//$this->load->library('qnbpay');

		$this->language->load('extension/payment/qnbpay');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$configs = $this->request->post;
			//dump($configs['payment_qnbpay_installments']);exit;
			if (isset($configs['payment_qnbpay_installments']) and count($configs['payment_qnbpay_installments']))
				$configs['payment_qnbpay_installments'] = strval(implode(',', $configs['payment_qnbpay_installments']));

			$x = $this->model_setting_setting->editSetting('payment_qnbpay', $configs);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/payment/qnbpay', 'user_token=' . $this->session->data['user_token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_test'] = $this->language->get('text_test');
		$data['text_live'] = $this->language->get('text_live');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_api_key'] = $this->language->get('entry_api_key');
		$data['entry_env_mode'] = $this->language->get('entry_env_mode');
		$data['entry_installment_list'] = $this->language->get('entry_installment_list');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['entry_merchant_key'] = $this->language->get('entry_merchant_key');
		$data['entry_app_key'] = $this->language->get('entry_app_key');
		$data['entry_app_secret'] = $this->language->get('entry_app_secret');
		$data['entry_merchant_id'] = $this->language->get('entry_merchant_id');
		$data['entry_sale_web_hook_key'] = $this->language->get('entry_sale_web_hook_key');
		$data['entry_refund_web_hook_key'] = $this->language->get('entry_refund_web_hook_key');
		$data['entry_recurring_web_hook_key'] = $this->language->get('entry_recurring_web_hook_key');
		$data['entry_environment'] = $this->language->get('entry_environment');
		$data['entry_provision'] = $this->language->get('entry_provision');
		$data['text_connected'] = $this->language->get('text_connected');
		$data['text_notconnected'] = $this->language->get('text_notconnected');
		$data['entry_installment_text'] = $this->language->get('entry_installment_text');


		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['login'])) {
			$data['error_login'] = $this->error['login'];
		} else {
			$data['error_login'] = '';
		}

		if (isset($this->error['key'])) {
			$data['error_key'] = $this->error['key'];
		} else {
			$data['error_key'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator' => ' :: '
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/payment/qnbpay', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator' => ' :: '
		);

		$data['action'] = HTTPS_SERVER . 'index.php?route=extension/payment/qnbpay&user_token=' . $this->session->data['user_token'];

		$data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/payment&user_token=' . $this->session->data['user_token'];

		$data['payment_qnbpay_merchant_key'] =
			isset($this->request->post['payment_qnbpay_merchant_key']) ? $this->request->post['payment_qnbpay_merchant_key'] : $this->config->get('payment_qnbpay_merchant_key');
		$data['payment_qnbpay_app_key'] =
			isset($this->request->post['payment_qnbpay_app_key']) ? $this->request->post['payment_qnbpay_app_key'] : $this->config->get('payment_qnbpay_app_key');
		$data['payment_qnbpay_app_secret'] =
			isset($this->request->post['payment_qnbpay_app_secret']) ? $this->request->post['payment_qnbpay_app_secret'] : $this->config->get('payment_qnbpay_app_secret');
		$data['payment_qnbpay_merchant_id'] =
			isset($this->request->post['payment_qnbpay_merchant_id']) ? $this->request->post['payment_qnbpay_merchant_id'] : $this->config->get('payment_qnbpay_merchant_id');
		$data['payment_qnbpay_sale_web_hook_key'] =
			isset($this->request->post['payment_qnbpay_sale_web_hook_key']) ? $this->request->post['payment_qnbpay_sale_web_hook_key'] : $this->config->get('payment_qnbpay_sale_web_hook_key');
		$data['payment_qnbpay_refund_web_hook_key'] =
			isset($this->request->post['payment_qnbpay_refund_web_hook_key']) ? $this->request->post['payment_qnbpay_refund_web_hook_key'] : $this->config->get('payment_qnbpay_refund_web_hook_key');
		$data['payment_qnbpay_recurring_web_hook_key'] =
			isset($this->request->post['payment_qnbpay_recurring_web_hook_key']) ? $this->request->post['payment_qnbpay_recurring_web_hook_key'] : $this->config->get('payment_qnbpay_recurring_web_hook_key');
		$data['payment_qnbpay_environment'] =
			isset($this->request->post['payment_qnbpay_environment']) ? $this->request->post['payment_qnbpay_environment'] : $this->config->get('payment_qnbpay_environment');
		$data['payment_qnbpay_provision'] =
			isset($this->request->post['payment_qnbpay_provision']) ? $this->request->post['payment_qnbpay_provision'] : $this->config->get('payment_qnbpay_provision');
		$data['payment_qnbpay_debug'] =
			isset($this->request->post['payment_qnbpay_debug']) ? $this->request->post['payment_qnbpay_debug'] : $this->config->get('payment_qnbpay_debug');
		$data['payment_qnbpay_status'] =
			isset($this->request->post['payment_qnbpay_status']) ? $this->request->post['payment_qnbpay_status'] : $this->config->get('payment_qnbpay_status');
		$data['payment_qnbpay_order_status_id'] =
			isset($this->request->post['payment_qnbpay_order_status_id']) ? $this->request->post['payment_qnbpay_order_status_id'] : $this->config->get('payment_qnbpay_order_status_id');
		$data['payment_qnbpay_sort_order'] =
			isset($this->request->post['payment_qnbpay_sort_order']) ? $this->request->post['payment_qnbpay_sort_order'] : $this->config->get('payment_qnbpay_sort_order');
		$data['payment_qnbpay_installments'] =
			isset($this->request->post['payment_qnbpay_installments']) ? $this->request->post['payment_qnbpay_installments'] : explode(",", $this->config->get('payment_qnbpay_installments'));
		$data['payment_qnbpay_token'] =
			isset($this->request->post['payment_qnbpay_token']) ? $this->request->post['payment_qnbpay_token'] : $this->config->get('payment_qnbpay_token');

		if ($data['payment_qnbpay_token'] == '') {
			$data['payment_qnbpay_token'] = $this->generateRandomString(10);
		}

		$data['payment_qnbpay_sale_web_hook_url'] = HTTPS_CATALOG . 'index.php?route=extension/payment/qnbpay/webhook&do=sale&token=' . $data['payment_qnbpay_token'];
		$data['payment_qnbpay_refund_web_hook_url'] = HTTPS_CATALOG . 'index.php?route=extension/payment/qnbpay/webhook&do=refund&token=' . $data['payment_qnbpay_token'];
		$data['payment_qnbpay_recurring_web_hook_url'] = HTTPS_CATALOG . 'index.php?route=extension/payment/qnbpay/webhook&do=recurring&token=' . $data['payment_qnbpay_token'];


		$data['connected'] = 0;
		$installmentOptions = [];
		if ($this->config->get('payment_qnbpay_merchant_key') != "") {

			$qnbpay = new qnbpay(
				$this->config->get('payment_qnbpay_app_key'),
				$this->config->get('payment_qnbpay_app_secret'),
				$this->config->get('payment_qnbpay_merchant_key'),
				$this->config->get('payment_qnbpay_sale_web_hook_key'),
				$this->config->get('payment_qnbpay_recurring_web_hook_key'),
				$this->config->get('payment_qnbpay_environment'),
				$this->config->get('payment_qnbpay_debug')
			);

			$qnbpayInstallments = $qnbpay->getStoreInstallments();

			if ($qnbpayInstallments["status"] == "success") {
				$data['connected'] = 1;
				$availableInstallments = $qnbpayInstallments["data"];
				foreach ($availableInstallments as $installment) {

					$installmentOptions[] = $installment;
				}
			}
		}

		$data['available_installments'] = $installmentOptions;

		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/qnbpay', $data));
	}

	public function install()
	{
		$this->load->model('extension/payment/qnbpay');
		$this->model_extension_payment_qnbpay->install();
	}

	public function uninstall()
	{
		$this->load->model('extension/payment/qnbpay');
		$this->model_extension_payment_qnbpay->uninstall();
	}

	protected function validate()
	{
		return true;
		if (!$this->user->hasPermission('modify', 'payment/qnbpay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['payment_qnbpay_merchant_name']) {
			$this->error['login'] = $this->language->get('error_login');
		}

		if (!$this->request->post['payment_qnbpay_merchant_key']) {
			$this->error['key'] = $this->language->get('error_key');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
