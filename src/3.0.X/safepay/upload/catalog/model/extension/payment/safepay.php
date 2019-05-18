<?php
class ModelExtensionPaymentSafepay extends Model {
	public function getMethod($address, $total) {
		$this->load->language('extension/payment/safepay');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_safepay_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('payment_safepay_total') > 0 && $this->config->get('payment_safepay_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('payment_safepay_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}
		
		$currencies = $this->allowedCurrencies();

		if (!in_array(strtoupper($this->session->data['currency']), $currencies)) {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'       => 'safepay',
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('payment_safepay_sort_order')
			);
		}

		return $method_data;
	}


	public function allowedCurrencies() {
		$currencies = array('PKR', 'USD', 'GBP', 'EUR', 'AUD', 'CNY');
		return $currencies;
	}


	public function logInfo($order_id, $key, $value) {

		$this->db->query("INSERT INTO " . DB_PREFIX . "safepay_order SET safepay_order_id = '" . (int)$order_id . "', meta_key = '" . $this->db->escape($key) . "', meta_value = '" . $this->db->escape($value) . "'");
	
	}
}
