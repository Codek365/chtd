<?php
use \Curl\Curl;

class ControllerExtensionModuleLazada extends Controller {
	private $error = array();

	private $api_key = 'e4x2LAzEki9xNGBI-ymcWXf8b43ck1gcMJXnGzZb_XMFB5JgOunPV2dV';

	private $userid  = 'codek365@gmail.com';


	public function index() {
		$this->load->language('extension/module/lazada');

		$this->document->setTitle("LAZADA");

		$this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('lazada', $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->cache->delete('product');

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/lazada', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/lazada', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/lazada', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/lazada', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		// get all products data from lazada
		$results = $this->GetLazadaData('GetProducts');



		if (isset($results) && $results != false) {
			foreach ($results['Products'] as $result) {
			// if (is_file(DIR_IMAGE . $result['image'])) {
			// 	$image = $this->model_tool_image->resize($result['image'], 40, 40);
			// } else {
			// 	$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			// }

			// $special = false;

			// $product_specials = $this->model_catalog_product->getProductSpecials($result['product_id']);

			// foreach ($product_specials  as $product_special) {
			// 	if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
			// 		$special = $this->currency->format($product_special['price'], $this->config->get('config_currency'));

			// 		break;
			// 	}
			// }
			foreach ($result['Skus'] as $key => $skus) {
				$data['products']['Skus'] = $skus;
			}
			$data['products'][] = array(
				// 'product_id' => $result['product_id'],
				'image'      => $result['Skus'][0]['Images'][0],
				'name'       => $result['Attributes']['name'],
				'model'      => isset($result['Attributes']['model']) ? $result['Attributes']['model'] : '',
				'price'      => $this->currency->format($result['Skus'][0]['price'], $this->config->get('config_currency')),
				'special'    => $this->currency->format($result['Skus'][0]['special_price'],$this->config->get('config_currency')),
				'quantity'   => $result['Skus'][0]['quantity'],
				'status'     => $result['Skus'][0]['Status'] == 'active' ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				// 'edit'       => $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'] . $url, true)
			);
		}
		}

		



	
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('extension/module/lazada', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/lazada')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}


		return !$this->error;
	}
	protected function GetLazadaData($action) {
		// Pay no attention to this statement.
		// It's only needed if timezone in php.ini is not set correctly.
		date_default_timezone_set("UTC");

		// The current time. Needed to create the Timestamp parameter below.
		$now = new DateTime();

		// The parameters for our GET request. These will get signed.
		$parameters = array(
		    // The user ID for which we are making the call.
		    'UserID' => $this->userid,

		    // The API version. Currently must be 1.0
		    'Version' => '1.0',

		    // The API method to call.
		    'Action' => $action,

		    // The format of the result.
		    'Format' => 'json',

		    // The current time formatted as ISO8601
		    'Timestamp' => $now->format(DateTime::ISO8601)
		);

		// Sort parameters by name.
		ksort($parameters);

		// URL encode the parameters.
		$encoded = array();
		foreach ($parameters as $name => $value) {
		    $encoded[] = rawurlencode($name) . '=' . rawurlencode($value);
		}
		// Concatenate the sorted and URL encoded parameters into a string.
		$concatenated = implode('&', $encoded);
		// The API key for the user as generated in the Seller Center GUI.
		// Must be an API key associated with the UserID parameter.
		// Compute signature and add it to the parameters.
		$parameters['Signature'] = rawurlencode(hash_hmac('sha256', $concatenated, $this->api_key, false));
		// Build Query String
		$queryString = http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);
		// Replace with the URL of your API host.
		$url = "https://api.sellercenter.lazada.vn?" . $queryString;

		$data = array();

		$curl = new Curl();
		$curl->get($url);
		if ($curl->error) {
		    $this->error['warning'] = 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
		} else {
			$callbackdata = $curl->response->SuccessResponse->Body;
			if ($curl->response == true) {
				$callbackdata = json_decode( json_encode($callbackdata), true);
				foreach ($callbackdata['Products'] as $key => $value) {
					array_push($data, $value['Attributes']);
				}
				return $callbackdata;
			} 
				
		}
		return !$this->error;
	}
}