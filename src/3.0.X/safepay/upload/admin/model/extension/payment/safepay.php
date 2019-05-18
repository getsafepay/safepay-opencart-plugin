<?php
class ModelExtensionPaymentSafepay extends Model {

	public function getSafePayOrder($order_id) {

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "safepay_order WHERE safepay_order_id = '" . (int)$order_id . "'");
		
		$safepay_info = array();

		foreach ($query->rows as $result) {
			$safepay_info[] = array(
				'meta_key' 	=> $result['meta_key'],
				'meta_value' =>	$result['meta_value']
			);
		}

		return $safepay_info;

	}

	public function install() {
		
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "safepay_order` (
				`meta_id` INT(11) NOT NULL AUTO_INCREMENT , 
				`safepay_order_id` INT(11) NOT NULL , 
				`meta_key` VARCHAR(255) NOT NULL , 
				`meta_value` TEXT NOT NULL , 
				PRIMARY KEY (`meta_id`)
			) ENGINE = InnoDB;
		");

	}


	public function uninstall() {

		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "safepay_order`;");

	}


}