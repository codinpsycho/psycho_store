<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Api extends RestController  {

	function __construct()
	{
        // Construct the parent class
		parent::__construct();
		$this->load->model('database');
		$this->load->library('form_validation');
	}

	public function coupon_post()
	{

		$this->form_validation->set_rules('secret_key', 'Secret Key', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE)
		{
			$error = $this->form_validation->error_array();
			$response = ['status' => false, 'message' => $error['secret_key']];
			$this->response($response, RESTController::HTTP_OK);
		}


		$secret_key = $this->input->post('secret_key');
		$provider = $this->database->_getProviderDetailUsingSecretKey($secret_key);

		if(empty($provider)) {

			$this->response( [
					'status' => false,
					'message' => 'No such provider found'
				], 404 );
		}

		$coupon = md5(mt_rand());
		$how_much = $provider['discount'];
		$expiry = date('Y-m-d', strtotime('+1 week'));
		$use_count = 1;

		$data = ['coupon' => $coupon, 'how_much' => $how_much, 'expiry' => $expiry, 'use_count' => $use_count];
		$this->database->AddDiscountCoupon($data);

		$response = ['status' => true, 'message' => 'coupon created successfully', 'data' => $data];
		$this->response( $response, RESTController::HTTP_OK );
	} 

}