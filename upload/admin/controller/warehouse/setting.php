<?php
class ControllerWarehouseSetting extends Controller {
	private $error = array();

	public function index() {
		// echo "string";
		// $this->install();
		$this->getList();
	}
	public function install()
	{
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_warehouse`");
		$sql = "CREATE TABLE `" . DB_PREFIX . "product_warehouse` (
				  `product_id` int(11) NOT NULL,
				  `model` varchar(64) NOT NULL,
				  `sku` varchar(64) NOT NULL,
				  `upc` varchar(12) NOT NULL,
				  `ean` varchar(14) NOT NULL,
				  `jan` varchar(13) NOT NULL,
				  `isbn` varchar(17) NOT NULL,
				  `mpn` varchar(64) NOT NULL,
				  `location` varchar(128) NOT NULL,
				  `quantity` int(4) NOT NULL DEFAULT '0',
				  `stock_status_id` int(11) NOT NULL,
				  `image` varchar(255) DEFAULT NULL,
				  `manufacturer_id` int(11) NOT NULL,
				  `shipping` tinyint(1) NOT NULL DEFAULT '1',
				  `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
				  `points` int(8) NOT NULL DEFAULT '0',
				  `tax_class_id` int(11) NOT NULL,
				  `date_available` date NOT NULL DEFAULT '0000-00-00',
				  `weight` decimal(15,8) NOT NULL DEFAULT '0.00000000',
				  `weight_class_id` int(11) NOT NULL DEFAULT '0',
				  `length` decimal(15,8) NOT NULL DEFAULT '0.00000000',
				  `width` decimal(15,8) NOT NULL DEFAULT '0.00000000',
				  `height` decimal(15,8) NOT NULL DEFAULT '0.00000000',
				  `length_class_id` int(11) NOT NULL DEFAULT '0',
				  `subtract` tinyint(1) NOT NULL DEFAULT '1',
				  `minimum` int(11) NOT NULL DEFAULT '1',
				  `sort_order` int(11) NOT NULL DEFAULT '0',
				  `status` tinyint(1) NOT NULL DEFAULT '0',
				  `viewed` int(5) NOT NULL DEFAULT '0',
				  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				  `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		
		$this->db->query($sql);
	}
	protected function getList() {
	
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('warehouse/setting', $data));
	}
}
