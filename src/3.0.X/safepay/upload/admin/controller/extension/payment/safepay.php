<?php
class ControllerExtensionPaymentSafepay extends Controller {
	private $error = array();



	public function getCurrenciesCodes() {
		$currencies = array();

		$this->load->model('localisation/currency');

		$registered_currencies = $this->model_localisation_currency->getCurrencies();

		if($registered_currencies && !empty($registered_currencies)) {
			foreach ($registered_currencies as $key => $currency) {
				if($currency['status'] == 1) {
					$currencies[] = $currency['code'];
				}
			}
		}

		return $currencies;
	}



	public function validateCurrencies($currencies = array()) {
		$allowed_currencies_codes = array('PKR', 'USD', 'GBP', 'EUR', 'AUD', 'CNY');
		foreach ($currencies as $currency) {
			if(in_array($currency, $allowed_currencies_codes)) {
				return false;
			}
		}
		return true;
	}

	public function index() {
		$this->load->language('extension/payment/safepay');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		$currencies = $this->getCurrenciesCodes();

		$data['stop_by_currency_error'] = $this->validateCurrencies($currencies);

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_safepay', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['safepay'])) {
			$data['error_safepay'] = $this->error['safepay'];
		} else {
			$data['error_safepay'] = array();
		}

		if (isset($this->error['payment_safepay_sandbox_apikey'])) {
			$data['error_payment_safepay_sandbox_apikey'] = $this->error['payment_safepay_sandbox_apikey'];
		} else {
			$data['error_payment_safepay_sandbox_apikey'] = '';
		}


		if (isset($this->error['payment_safepay_apikey'])) {
			$data['error_payment_safepay_apikey'] = $this->error['payment_safepay_apikey'];
		} else {
			$data['error_payment_safepay_apikey'] = '';
		}


		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/safepay', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/safepay', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

		$this->load->model('localisation/language');

		$data['payment_safepay'] = array();

		if (isset($this->request->post['payment_safepay_total'])) {
			$data['payment_safepay_total'] = $this->request->post['payment_safepay_total'];
		} else {
			$data['payment_safepay_total'] = $this->config->get('payment_safepay_total');
		}


		if (isset($this->request->post['payment_safepay_mode'])) {
			$data['payment_safepay_mode'] = $this->request->post['payment_safepay_mode'];
		} else {
			$data['payment_safepay_mode'] = $this->config->get('payment_safepay_mode');
		}

		if (isset($this->request->post['payment_safepay_sandbox_apikey'])) {
			$data['payment_safepay_sandbox_apikey'] = $this->request->post['payment_safepay_sandbox_apikey'];
		} else {
			$data['payment_safepay_sandbox_apikey'] = $this->config->get('payment_safepay_sandbox_apikey');
		}

		if (isset($this->request->post['payment_safepay_apikey'])) {
			$data['payment_safepay_apikey'] = $this->request->post['payment_safepay_apikey'];
		} else {
			$data['payment_safepay_apikey'] = $this->config->get('payment_safepay_apikey');
		}


		if (isset($this->request->post['payment_safepay_order_status_id'])) {
			$data['payment_safepay_order_status_id'] = $this->request->post['payment_safepay_order_status_id'];
		} else {
			$data['payment_safepay_order_status_id'] = $this->config->get('payment_safepay_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_safepay_geo_zone_id'])) {
			$data['payment_safepay_geo_zone_id'] = $this->request->post['payment_safepay_geo_zone_id'];
		} else {
			$data['payment_safepay_geo_zone_id'] = $this->config->get('payment_safepay_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['payment_safepay_status'])) {
			$data['payment_safepay_status'] = $this->request->post['payment_safepay_status'];
		} else {
			$data['payment_safepay_status'] = $this->config->get('payment_safepay_status');
		}

		if (isset($this->request->post['payment_safepay_sort_order'])) {
			$data['payment_safepay_sort_order'] = $this->request->post['payment_safepay_sort_order'];
		} else {
			$data['payment_safepay_sort_order'] = $this->config->get('payment_safepay_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/safepay', $data));
	}

	public function install() {
		$this->load->model('extension/payment/safepay');
		$this->model_extension_payment_safepay->install();
	}

	public function uninstall() {
		$this->load->model('extension/payment/safepay');
		$this->model_extension_payment_safepay->uninstall();
	}

	public function order() {
		if ($this->config->get('payment_safepay_status')) {
			$this->load->language('extension/payment/safepay');

			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$this->load->model('extension/payment/safepay');
			$safepay_info = $this->model_extension_payment_safepay->getSafePayOrder($order_id);

			if ($safepay_info) {
				
				$data['meta_values'] = $safepay_info;
				return $this->load->view('extension/payment/safepay_order', $data);
				
			}
		}
	}


	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/safepay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['payment_safepay_sandbox_apikey']) {
			$this->error['payment_safepay_sandbox_apikey'] = $this->language->get('error_required');
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		if (!$this->request->post['payment_safepay_apikey']) {
			$this->error['payment_safepay_apikey'] = $this->language->get('error_required');
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}
}
?>