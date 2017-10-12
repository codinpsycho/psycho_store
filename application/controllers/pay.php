<?php 
/**
* 	
*/
require APPPATH.'third_party/razorpay-php/Razorpay.php';
use Razorpay\Api\Api;

class Pay extends CI_controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('database');
		$this->load->model('tank_auth/users');
		$this->load->helper('url');
		$this->load->helper('html');
		$this->load->helper('form');
		$this->load->helper('psycho_helper');
		$this->load->helper('mailgun_helper');
		$this->load->library('tank_auth');
		$this->load->library('cart');
		$this->load->helper('email');
		$this->config->load('gateway_settings');
	}

	function index()
	{
		display('pay', null);
		
	}

	function process_payment()
	{
		$rzp_key = $this->config->item('rzp_merchant_key');
		$rzp_secret = $this->config->item('rzp_merchant_secret');
		$rzp_payment_id = $this->input->post('rzp_payment_id');
		$amount = $this->input->post('pay_amount')*100; 	//Amount in paisa

		$api = new Api($rzp_key, $rzp_secret);

		$payment = $api->payment->fetch($rzp_payment_id);
		$payment->capture(array('amount' => $amount));
		
		//Captured, now just redirect like we do in COD orders
		redirect('pay/done');
	}

	function done()
	{
		$data['heading'] = "And it's Done";
		$data['content'] = "Thanks for being so awesome. Don't forget to collect your merch and spread your geekiness all around
							<a class=\"btn btn-default\" href=\"pay\"> Continue </a>";
		display('basic', $data);
	}
}

?>