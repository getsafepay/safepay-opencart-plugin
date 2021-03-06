<?php
class ControllerPaymentSafepay extends Controller {

	public function index() {
		$this->load->language('payment/safepay');
		$this->load->model('checkout/order');

		$data['text_title'] = $this->language->get('text_title');  
		$data['text_sandbox_mode_enable']  =  $this->language->get('text_sandbox_mode_enable');
		$data['text_currency_not_supported']  =  $this->language->get('text_currency_not_supported');
		$data['safepay_mode'] = $this->config->get('safepay_mode');
		$data['currency'] = $this->session->data['currency'];
		$data['safepay_sandbox_apikey'] = $this->config->get('safepay_sandbox_apikey');
		$data['safepay_apikey'] = $this->config->get('safepay_apikey');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		$data['order_info'] = $order_info;
		$this->load->model('localisation/country');
		$country_info = $this->model_localisation_country->getCountry($order_info['payment_country_id']);
		$data['country'] = $country_info['iso_code_2'];
		$this->load->model('localisation/zone');
		$zone_info = $this->model_localisation_zone->getZone($order_info['payment_zone_id']);
		$data['province'] = $zone_info['code'];
		$data['total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		return $this->load->view('payment/safepay', $data);
	}

	protected function validateCallback($tracker = false) {
		if($tracker) {
			$payment_safepay_mode = $this->config->get('payment_safepay_mode');
			if($payment_safepay_mode == 'sandbox') {
				$url = "https://sandbox.api.getsafepay.com/order/v1/".$tracker;
			} else {
				$url = "https://api.getsafepay.com/order/v1/".$tracker;
			}
			$ch =  curl_init($url);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
		    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)');
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$result = curl_exec($ch);
			if (curl_errno($ch)) { 
			   return false;
			}
			curl_close($ch);
			$result_array = json_decode($result);
			if(empty($result_array->status->errors)) {
				$state = $result_array->data->state;
				if($state === "TRACKER_ENDED") {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} 
	}
	
	public function confirm() {
		$json = array();
		if ($this->session->data['payment_method']['code'] ==  'safepay') {
			$this->load->language('payment/safepay');
			$this->load->model('payment/safepay');
			$data = $this->request->post;
			foreach ($data as $key => $value) {
				 $this->model_payment_safepay->logInfo($this->session->data['order_id'], $key, $value);
			}
			$this->load->model('checkout/order');
			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('safepay_order_status_id'));
			$json['redirect'] = $this->url->link('checkout/success');
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));		
	}

}