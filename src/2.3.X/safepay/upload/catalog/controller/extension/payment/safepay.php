<?php
class ControllerExtensionPaymentSafepay extends Controller {

	public function index() {
		$this->load->language('extension/payment/safepay');
		$this->load->model('checkout/order');

		$data['text_title'] = $this->language->get('text_title');  
		$data['text_sandbox_mode_enable']  =  $this->language->get('text_sandbox_mode_enable');
		$data['text_currency_not_supported']  =  $this->language->get('text_currency_not_supported');
		$data['payment_safepay_mode'] = $this->config->get('payment_safepay_mode');
		$data['currency'] = $this->session->data['currency'];
		$data['payment_safepay_sandbox_apikey'] = $this->config->get('payment_safepay_sandbox_apikey');
		$data['payment_safepay_apikey'] = $this->config->get('payment_safepay_apikey');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		$data['total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		return $this->load->view('extension/payment/safepay', $data);
	}


	public function confirm() {
		$json = array();
		if ($this->session->data['payment_method']['code'] == 'safepay') {
			$this->load->language('extension/payment/safepay');
			$this->load->model('extension/payment/safepay');
			$data = $this->request->post;
			foreach ($data as $key => $value) {
				 $this->model_extension_payment_safepay->logInfo($this->session->data['order_id'], $key, $value);
			}
			$this->load->model('checkout/order');
			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_safepay_order_status_id'));
			$json['redirect'] = $this->url->link('checkout/success');
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));		
	}

}