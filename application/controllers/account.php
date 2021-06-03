<?php 
class Account extends CI_controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('database');
		$this->load->helper('url');
		$this->load->library('tank_auth');		
	}

	function index()
	{

	}

	//Displays previous orders for currently logged in user
	function orders()
	{
		//Get logged in user
		$userid = $this->tank_auth->get_user_id();
		//Get orders for logged in user
		echo $userid;
		$data['orders'] = $this->database->GetOrdersForUser($userid);
		
		$this->load->view('account', $data);
		
	}

	function view($orderid)
	{
		
	}
}
?>