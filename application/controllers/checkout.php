<?php 
/**
* 
*/
require APPPATH.'third_party/razorpay-php/Razorpay.php';
use Razorpay\Api\Api;

class checkout extends CI_controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('tank_auth');
		$this->load->library('cart');
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->helper('html');		
		$this->load->helper('psycho_helper');
		$this->load->helper('mailgun_helper');
		$this->load->model('database');
		$this->config->load('gateway_settings');
	}

	function index()
	{
		$this->_start_checkout();		
	}

	function _start_checkout()
	{
		if($this->_is_active_txn_id_valid() == false)
		{
			$this->_create_checkout_order();
		}

		$checkout_order = $this->_get_active_checkout_order();

		//Make sure active checkout_order is not locked
		if($checkout_order['state'] == 'locked')
		{
			$this->_create_checkout_order();
		}

		$this->_save_cart_items();
	
		$this->login();
	}

	function _create_checkout_order()
	{
		$txn_id = $this->_generate_txnid();

		$this->database->SaveTxnIdOnCheckout($txn_id);
		
		//Set txn_id in session
		$this->session->set_userdata('txn_id', $txn_id);
	}

	function _save_cart_items()
	{
		//Try applying domain based discount before saving
		check_domain_discount();

		$txn_id = $this->session->userdata('txn_id');

		//Empty checkout_items for this txn_id		
		$this->database->RemoveCheckoutItemsForTxnId($txn_id);

		$this->database->SaveAmountOnCheckout($this->cart->final_price(), $txn_id);

		//Save cart items
		foreach ($this->cart->contents() as $item)
		{
			$checkout_item = array
						(
							'txn_id'		=>	$txn_id,
							'product_id'	=> 	$item['id'],
							'count'			=> 	$item['qty'],
							'option'		=> 	$item['options']['extra'],
						);
			
			$this->database->SaveCartItemOnCheckout($checkout_item);
		}
	}

	function _save_user_details()
	{
		//Save address and user id
		$txn_id = $this->session->userdata('txn_id');
		$this->database->SaveUserIdOnCheckout($this->tank_auth->get_user_id(), $txn_id);

		//Address must be there else _validate_address() will fail
	}	

	function login()
	{
		$this->_validate_cart();

		if(!$this->tank_auth->is_logged_in())
		{
			redirect('auth/login?redirect_url='.rawurlencode('checkout/'));
		}
		else
		{
			redirect('checkout/address');
		}
	}

	function address()
	{
		$this->_validate_cart();

		$user_id = $this->tank_auth->get_user_id();
		
		if($this->tank_auth->is_logged_in())
		{
			$this->_save_user_details();

			$result = $this->database->GetAddressesForUser($user_id);
			
			if(count($result) == 0 )	//Directly send the user to address entry page
			{
				redirect('auth/register_address');
			}

			$data['addresses'] = $result;
			display('address',$data);
		}
		else
		{
			redirect('checkout/');
		}
		
	}

	function _is_active_txn_id_valid()
	{
		$is_txn_id_valid = false;
		$txn_id = $this->session->userdata('txn_id');
		if($txn_id)
		{
			//Make sure it exists in db also
			$checkout_order = $this->database->GetCheckoutOrder($txn_id);

			if(count($checkout_order))
			{
				$is_txn_id_valid = true;
			}
		}
		
		return $is_txn_id_valid;
	}

	function _validate_cart()
	{
		//Make sure txn_id is generated	
		$txn_id = $this->_is_active_txn_id_valid();

		$out_of_stock = false;

		foreach ($this->cart->contents() as $items)
		{
			$prod_id = $items['id'];
			$product = $this->database->GetProductById($prod_id);

			$out_of_stock = $this->_is_out_of_stock($product, $items);

			if($out_of_stock == true)
			{
				break;
			}
		}

		if($this->cart->total_items() <= 0 || $out_of_stock || ($txn_id == false) )
		{			
			redirect('cart/');
		}		
	}

	function _is_out_of_stock($product, $cart_item)
	{

		switch ($product['product_type'])
		{
			case 'hoodie':
			case 'tshirt':
				$size_in_stock = $product['product_details'][strtolower($cart_item['options']['extra']).'_qty'];
				
				if($product['product_details']['size_preorder'] == false && $cart_item['qty'] > $size_in_stock)
					return true;
				break;
			
			default:
				# code...
				break;
		}

		return false;
	}

	function _validate_user()
	{
		if(!$this->tank_auth->is_logged_in())
		{			
			redirect('checkout/');
		}
	}

	//makes sure an addrees_id is set in db and that it belongs to current signed-in user
	function _validate_address()
	{
		$txn_id = $this->session->userdata('txn_id');
		$checkout_order = $this->database->GetCheckoutOrder($txn_id);

		$address_id = $checkout_order['address_id'];

		if($this->_is_address_valid_for_current_user($address_id) == false)
			redirect('checkout/');
	}

	function _is_address_valid_for_current_user($address_id)
	{
		$address = $this->database->GetAddressById($address_id);

		//We also need to make sure address belongs to the currently signed-in user		
		$current_users_addresses = $this->database->GetAddressesForUser($this->tank_auth->get_user_id());
		$address_valid = false;
		foreach ($current_users_addresses as $key => $address)
		{
			if($address['address_id'] == $address_id)
			{				
				$address_valid = true;
				break;
			}
		}

		return $address_valid;
	}

	function _get_active_checkout_order()
	{
		$txn_id = $this->session->userdata('txn_id');
		return $this->database->GetCheckoutOrder($txn_id);
	}

	//It means we are going for online payment, dont modify me now
	function _lock_active_checkout_order()
	{
		$checkout_order = $this->_get_active_checkout_order();		
		$this->database->LockCheckoutOrder($checkout_order['txn_id']);
	}

	//Store the address in database
	//Also makes sure that address passed is valid for current signed-in user
	function save_address()
	{
		$address_id = $this->input->post('address_id');		

		$address_valid = $this->_is_address_valid_for_current_user($address_id);

		if($address_valid)
		{
			//We need to be here to show the review page, else we go again to address page
			//to get correct address			
			$this->database->SaveAddressOnCheckout($address_id,$this->session->userdata('txn_id'));			
			redirect('checkout/review');
		}

		redirect('checkout/');
	}	

	function review()
	{
		$this->_validate_cart();
		$this->_validate_address();

		//make sure address is set in checkout_db
		$checkout_order = $this->_get_active_checkout_order();
		$user = $this->database->GetUserById($checkout_order['user_id']);

		$user = $this->database->GetUserById($checkout_order['user_id']);

		if( is_null($checkout_order['address_id'] ))
		{
			redirect('checkout/');
		}

		$address = $this->database->GetAddressById($checkout_order['address_id']);		
		$shipping_details = $this->database->GetShippingDetails($address['pincode']);
		$shipping_available = false;
		$cod_available = false;

		if($shipping_details)
		{
			$shipping_available = true;
			
			if($shipping_details['cod'] === 'Y')
			{
				$cod_available = true;
			}	
		}

		$data['txn_id'] = $checkout_order['txn_id'];
		$data['email'] = $user['email'];
 		$data['raw_address'] = $address;
 		$data['formatted_address'] = format_address($address);
		$data['shipping_available'] = $shipping_available;
		$data['cod_available'] = $cod_available;
		$data['cod_charges'] = $this->config->item('cod_charge');

		//Add 'notes' for Razorpay
		$data['txn_id'] = $checkout_order['txn_id'];
		
		
		display('review', $data);
	}

	function payment()
	{
		$this->_validate_cart();
		$this->_validate_user();
		$this->_validate_address();

		//Save stuff again on last step before leaving the site/placing order
		$this->_save_cart_items();
		$this->_save_user_details();

		$payment_mode = $this->input->post('payment_mode');

		//Once we get the correct payment mode, then lock and fire
		//Locking inside switch is imp.
		switch ($payment_mode)
		{			
			case 'cod':
				$this->_lock_active_checkout_order();
				$this->session->set_flashdata('ok_to_order', true);
				redirect('checkout/place_order');
				break;

			case 'pre-paid':
				$this->_lock_active_checkout_order();
				$this->_payment_gateway($this->input->post());
				break;
			
			default:
				redirect('checkout/');
				break;
		}
	}

	function place_order()
	{
		$ok_to_place_order = false;

		$order_info_params = array();

		//Verify checksum (not sure abt this, might be unnecessary)
		if($this->input->post( 'key' ) != (string)false )
		{
			//We came here through online transaction
			$returned_hash	 	= $this->input->post( 'hash' );
			$status 			= $this->input->post('status');
			
			//<SALT>|status||||||udf5|udf4|udf3|udf2|udf1|email|firstname|productinfo|amount|txnid|key
			$hash_string = $this->input->post('additionalCharges').'|'.$this->input->post('salt').'|'.$status.'|'.'||||||||||'.'|'.$this->input->post('email').'|'.$this->input->post('firstname').'|'.$this->input->post('productinfo').'|'.$this->input->post('amount').'|'.$this->input->post('txnid').'|'.$this->input->post('key');

			$hash = strtolower(hash('sha512', $hash_string));
			
			if($this->input->post( 'status' ) === "success")
			{
				$ok_to_place_order = true;

				$order_info_params = $this->input->post();	//For PayU
			}
		}
		else
		{
			//For COD and Razorpay
			$ok_to_place_order = $this->session->flashdata('ok_to_order');
			$order_info_params['rzp_payment_id'] = $this->session->flashdata('rzp_payment_id');
		}		

		if($ok_to_place_order)
		{
			$order_info = $this->_generate_orderinfo($order_info_params);
			$this->_place_order($order_info);
			$this->_reward_user($order_info);
			$this->_send_order_mail($order_info);

			redirect('checkout/success');
		}
		else
		{
			redirect('checkout');
		}	
	}

	function _payment_gateway($post_params)
	{
		switch ($this->config->item('payment_gateway'))
		{
			case 'razorpay':
				$this->_process_razorpay($post_params);
				break;
			
			case 'payu':
				$this->_process_payu();
				break;
			
			default:
				# code...
				break;
		}
	}

	function _process_razorpay($post_params)
	{
		//Need to capture the payment using the payment id
		$checkout_order = $this->_get_active_checkout_order();

		$rzp_key = $this->config->item('rzp_merchant_key');
		$rzp_secret = $this->config->item('rzp_merchant_secret');
		$rzp_payment_id = $post_params['rzp_payment_id'];
		$amount = $checkout_order['order_amount']*100; 	//Amount in paisa

		$api = new Api($rzp_key, $rzp_secret);

		$payment = $api->payment->fetch($rzp_payment_id);
		$payment->capture(array('amount' => $amount));
		
		//Captured, now just redirect like we do in COD orders
		$this->session->set_flashdata('ok_to_order', true);
		$this->session->set_flashdata('rzp_payment_id', $rzp_payment_id);
		redirect('checkout/place_order');
	}

	function process_payu()
	{
		$gateway_params = array();
		
		$checkout_order = $this->_get_active_checkout_order();

		//Gateway config
		$gateway_params['key'] = $this->config->item('merchant_key');
		$gateway_params['salt'] = $this->config->item('salt');			
		$gateway_params['surl'] = $this->config->item('success_url');
		$gateway_params['furl'] = $this->config->item('failure_url');
		$gateway_params['txnid'] = $checkout_order['txn_id'];
		$gateway_params['service_provider'] = $this->config->item('service_provider');

		//Site specific info				
		$address = $this->database->GetAddressById($checkout_order['address_id']);
		$user = $this->database->GetUserById($checkout_order['user_id']);

		$gateway_params['amount'] = $checkout_order['order_amount'];
		$gateway_params['firstname'] = $address['first_name'];
		$gateway_params['lastname'] = $address['last_name'];
		$gateway_params['address1'] = $address['address_1'];
		$gateway_params['address2'] = $address['address_2'];
		$gateway_params['city'] = $address['city'];
		$gateway_params['state'] = $address['state'];
		$gateway_params['country'] = $address['country'];
		$gateway_params['zipcode'] = $address['pincode'];
		$gateway_params['email'] = $user['email'];
		$gateway_params['phone'] = $address['phone_number'];
		$gateway_params['productinfo'] = "Psycho Store Merchandise";	//To be added


		//Generate hash
		//key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10
		$hash_string = $gateway_params['key'].'|'.$gateway_params['txnid'].'|'.$gateway_params['amount'].'|'.$gateway_params['productinfo'].'|'.$gateway_params['firstname'].'|'.$gateway_params['email'].'|'.'||||||||||'.$gateway_params['salt'];

		$gateway_params['hash'] = strtolower(hash('sha512', $hash_string));

		//Do a post request
		$url = $this->config->item('gateway_url');
		
		// Create a connection
		$ch = curl_init($url);

		// Form post string
		$postString = http_build_query($gateway_params);

		// Setting our options
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);

		// Get the response
		$res = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		redirect($info['redirect_url']);
	}

	function success()
	{
		$success = $this->load->view("success", null, TRUE);
		$data = array('heading' => "Minions Assemble Now" );
		$data['content'] = $success;
		display('message', $data);
	}

	function failure()
	{
		$fail = $this->load->view("failure", null, TRUE);
		$data = array('heading' => "Uh Oh ... Damnit" );
		$data['content'] = $fail;
		display('message', $data);
	}

	function _reward_user($order_info)
	{
		$user = $order_info['user'];
		$divider = 10;

		switch ($order_info['payment_mode'])
		{
			case 'pre-paid':
				$divider = 10;	//10%
				break;
			
			case 'cod':
				$divider = 20;	//5%
				break;
		}
		
		$points = $user['points'] + $order_info['amount']/$divider;
		$this->database->RewardUser($order_info['user_id'], $points);
	}

	function _send_order_mail($order_info)
	{
		//Detects order num for a particular user and sends a mail accordingly
		$user = $order_info['user'];
		$orders = $this->database->GetOrdersForUser($order_info['user_id']);
		$order_num = count($orders);

		$data['site_name'] = $this->config->item('website_name', 'tank_auth');
		$data['username'] = $user['username'];
		$data['order_id'] = $order_info['txn_id'];
		$data['product_table'] = generate_product_table_for_order($order_info['txn_id']);
		$data['address'] = format_address($order_info['address']);
		$data['payment_mode'] = $order_info['payment_mode'];
		
		//For special mails
		switch ($order_num)
		{
			case '1':
				$params = mg_create_mail_params('first_order', $data);
				mg_send_mail($user['email'], $params);
				break;

			case '2':
				$params = mg_create_mail_params('second_order', $data);
				mg_send_mail($user['email'], $params);
				break;

			case '3':
				$params = mg_create_mail_params('third_order', $data);
				mg_send_mail($user['email'], $params);
				break;
			
			case '4':
				$params = mg_create_mail_params('fourth_order', $data);
				mg_send_mail($user['email'], $params);
				break;

			case '5':
				$params = mg_create_mail_params('fifth_order', $data);
				mg_send_mail($user['email'], $params);
				break;

			
			default:
				# code...
				break;
		}

		//This is to be sent for each order
		$params = mg_create_mail_params('order', $data);
		mg_send_mail($user['email'], $params);
	}	


	function _place_order($order_info)
	{
		$order = array
				(
					'txn_id'		=>	$order_info['txn_id'],
					'user_id'		=>	$order_info['user_id'],
					'address_id' 	=> 	$order_info['address_id'],
					'payment_mode'	=>	$order_info['payment_mode'],
					'order_amount'	=>	$order_info['amount'],
					//'order_status'=>	Default set as pending
				);
		
		$this->database->AddOrder($order);
		
		$checkout_items = $order_info['checkout_items'];

		foreach ($checkout_items as $item)
		{
			$order_item = array
						(
							'txn_id'		=>	$order_info['txn_id'],
							'product_id'	=> 	$item['product_id'],
							'count'			=> 	$item['count'],
							'option'		=> 	$item['option'],
						);
			

			//Update database
			$this->_update_product_info($item);
			$this->database->AddOrderItem($order_item);

			//Consume code
			if($this->cart->is_discount_applied())
			{
				$disc_info = $this->cart->discount_info();
				$this->database->ConsumeCode($disc_info['coupon']);
			}
			
		}

		//Destroy stuff now
		$this->cart->destroy();
		$this->session->unset_userdata('txn_id');
		$this->database->CheckoutDone($order_info['txn_id']);
	}

	function _update_product_info($checkout_item)
	{
		$product = $this->database->GetProductById($checkout_item['product_id']);
		$product['product_qty_sold'] += $checkout_item['count'];
		
		//Update product info
		switch ($product['product_type'])
		{
			case 'hoodie':
			case 'tshirt':
				$size = $checkout_item['option'];
				$size = strtolower($size).'_qty';
				$product['product_details'][$size] -= $checkout_item['count'];
				$this->database->ModifyProduct($product);
				break;
			
			default:
				# code...
				break;
		}
	}

	function _generate_orderinfo($post_back_params)
	{
		$order_info = array();

		//Payment Mode
		if( isset($post_back_params['mode']) )	//For PayU
		{
			$order_info['payment_mode'] = 'pre-paid';

			//Its v.v.imp to take txnid from post_back_params, because session txnid can be modified
			//when coming back from payment gateway
			$txn_id = $post_back_params['txnid'];
			$checkout_order = $this->database->GetCheckoutOrder($txn_id);
		}
		else if( $post_back_params['rzp_payment_id'] )	//For Razorpay
		{
			$order_info['payment_mode'] = 'pre-paid';
			$checkout_order = $this->_get_active_checkout_order();
		}
		else
		{
			$order_info['payment_mode'] = 'cod';
			$checkout_order = $this->_get_active_checkout_order();
			$checkout_order['order_amount'] += $this->config->item('cod_charge') ;
		}		

		$order_info['txn_id'] = $checkout_order['txn_id'];
		$order_info['amount'] = $checkout_order['order_amount'];
		$order_info['address_id'] = $checkout_order['address_id'];
		$order_info['user_id'] = $checkout_order['user_id'];
		$order_info['checkout_items'] = $this->database->GetCheckoutOrderItems($order_info['txn_id']);
		$order_info['user'] = $this->database->GetUserById($checkout_order['user_id']);
		$order_info['address'] = $this->database->GetAddressById($checkout_order['address_id']);

		return $order_info;
	}

	function _generate_txnid()
	{
		//return substr(hash('sha256', mt_rand() . microtime()), 0, 10);
		return dechex(time());	//makes the txnid smaller
	}
}
?>