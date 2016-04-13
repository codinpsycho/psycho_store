<?php 
/**
* 
*/
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
							'size'			=> 	$item['options']['Size'],
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

			//Check stock and set stock info
			$data['products'][$items['rowid'].'stock_state'] = "";

			if( $product['product_type'] == "Tshirt" || $product['product_type'] == "Hoodie")
			{
				$size = $items['options']['Size'];
				$size_in_stock = $product['product_count_'.strtolower($size)];
			}
			else
			{
				//For product woth no size info like action figures .. later on
			}

			$size_preorder_allowed = $product['size_preorder'];

			if($size_preorder_allowed == false && $items['qty'] > $size_in_stock)
			{
				$out_of_stock = true;
				break;
			}
		}

		if($this->cart->total_items() <= 0 || $out_of_stock || ($txn_id == false) )
		{			
			redirect('cart/');
		}		
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

		$data['shipping_available'] = $shipping_available;
		$data['cod_available'] = $cod_available;
		$data['address'] = format_address($address);
		
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
				$this->_payment_gateway();
				break;
			
			default:
				redirect('checkout/');
				break;
		}
	}

	function place_order()
	{
		$ok_to_place_order = false;		

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
			}
		}
		else
		{
			$ok_to_place_order = $this->session->flashdata('ok_to_order');
		}		

		if($ok_to_place_order)
		{
			$order_info = $this->_generate_orderinfo($this->input->post());
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
			
			default:
				# code...
				break;
		}

		//This is to be sent for each order
		$params = mg_create_mail_params('order', $data);
		mg_send_mail($user['email'], $params);
	}

	function _payment_gateway()
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
							'size'			=> 	$item['size'],
						);

			//Update product info
			$size = $item['size'];
			$size = 'product_count_'.strtolower($size);
			$product = $this->database->GetProductById($item['product_id']);
			$product['product_qty_sold'] += $item['count'];
			$product[$size] -= $item['count'];	//To be done for products with no size

			//Update database
			$this->database->ModifyProduct($product);
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

	function _generate_orderinfo($post_back_params)
	{
		$order_info = array();

		//Payment Mode
		if( isset($post_back_params['mode']) )
		{
			$order_info['payment_mode'] = 'pre-paid';

			//Its v.v.imp to take txnid from post_back_params, because session txnid can be modified
			//when coming back from payment gateway
			$txn_id = $post_back_params['txnid'];
			$checkout_order = $this->database->GetCheckoutOrder($txn_id);
		}
		else
		{
			$order_info['payment_mode'] = 'cod';
			$checkout_order = $this->_get_active_checkout_order();
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