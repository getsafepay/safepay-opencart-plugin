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

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['heading_title']   = $this->language->get('heading_title'); 
		$data['text_extension']   = $this->language->get('text_extension');     
		$data['text_success']   = $this->language->get('text_success');       
		$data['text_edit']   = $this->language->get('text_edit');          
		$data['text_safepay']   = $this->language->get('text_safepay');	  	 
		$data['text_sandbox']   = $this->language->get('text_sandbox');       
		$data['text_production']   = $this->language->get('text_production');   
		$data['text_all_zones']   = $this->language->get('text_all_zones'); 	 
		$data['text_enabled']   = $this->language->get('text_enabled'); 	 
		$data['text_disabled']   = $this->language->get('text_disabled'); 	

		$data['entry_total']   = $this->language->get('entry_total');        		
		$data['entry_order_status']   = $this->language->get('entry_order_status'); 		
		$data['entry_geo_zone']   = $this->language->get('entry_geo_zone');     		
		$data['entry_status']   = $this->language->get('entry_status');       		
		$data['entry_sort_order']   = $this->language->get('entry_sort_order');   		
		$data['entry_sandbox_apikey']   = $this->language->get('entry_sandbox_apikey');  	
		$data['entry_apikey']   = $this->language->get('entry_apikey');   			
		$data['entry_safepay_mode']   = $this->language->get('entry_safepay_mode');   

		$data['tab_general']   = $this->language->get('tab_general');   			 
		$data['tab_order_status']   = $this->language->get('tab_order_status');   		 
		$data['tab_safepay_settings']   = $this->language->get('tab_safepay_settings');  	 
		$data['tab_order_safepay_details']   = $this->language->get('tab_order_safepay_details');  

		$data['help_total']   = $this->language->get('help_total');         		
		$data['help_order_status']   = $this->language->get('help_order_status');  		
		$data['help_cancel_order_status']   = $this->language->get('help_cancel_order_status');  

		$data['error_permission']   = $this->language->get('error_permission');   
		$data['error_required']   = $this->language->get('error_required');     
		$data['error_currency']   = $this->language->get('error_currency'); 	 
		$data['error_warning']   = $this->language->get('error_warning'); 	 


		$currencies = $this->getCurrenciesCodes();

		$data['stop_by_currency_error'] = $this->validateCurrencies($currencies);

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('safepay', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true));
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
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/safepay', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/safepay', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);

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

		if (isset($this->request->post['safepay_status'])) {
			$data['safepay_status'] = $this->request->post['safepay_status'];
		} else {
			$data['safepay_status'] = $this->config->get('safepay_status');
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
		if ($this->config->get('safepay_status')) {
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