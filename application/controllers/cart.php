<?php 
/**
* 
*/
class Cart extends CI_controller
{
	var $auto_disc_array = array(	'2' => array('percentage' => 5, 'coupon' => 'auto_disc_5', 'comment' =>" Btw, adding another item to your cart will give an instant 5% off"),
		'3' => array('percentage' => 10, 'coupon' => 'auto_disc_10', 'comment' => " See, magic happens, now add another item for a 10% off."),
	);	

	function __construct()
	{
		parent::__construct();
		$this->load->library('cart');
		$this->load->model('database');
		// $this->load->helper('url');
		$this->load->helper('html');		
		$this->load->helper('form');
		$this->load->helper('psycho_helper');
		$this->load->library('session');
		$this->load->library('tank_auth');
	}

	function index()
	{		
		$this->view();
	}

	//make sure user cant enter more than available stock qty
	function _set_stock_info(&$data)
	{
		if(!empty($this->cart->contents())) {
			foreach ($this->cart->contents() as $item)
			{
				$prod_id = $item['id'];
				$product = $this->database->GetProductById($prod_id);

			//Check stock and set stock info
				$data['products'][$item['rowid'].'stock_state'] = $this->_set_stock_state($product, $item);

				$data['products'][$prod_id] = $product;
			}
		}
	}

	function _set_stock_state($product, $cart_item)
	{

		switch ($product['product_type'])
		{
			case 'hoodie':
			case 'tshirt':
			$size_in_stock = $product['product_details'][strtolower($cart_item['options']['extra']).'_qty'];

			if($product['product_details']['size_preorder'] == false && $cart_item['qty'] > $size_in_stock)
				return "Out Of Stock";
			break;
			
			default:
				# code...
			break;
		}

		return "";

	}

	//Show items in cart
	function view()
	{
		$data[] = 0;
		$num_items = $this->cart->total_items();
		generate_header($data);
		$this->_set_stock_info($data);

		check_domain_discount();

		if($num_items)
		{
			//$this->_show_cheat_code_after_timeout(5000);
		}

		if($this->config->item('auto_discounts'))
		{
			$this->_apply_auto_disc();
		}

		$data['cheat_hints'] = $this->load->view('cheatcode_hints', null, true);
		display('cart',$data);
	}

	function _show_cheat_code_after_timeout($timeout)
	{
		$is_discount_applied = $this->cart->is_discount_applied();

		if($is_discount_applied == false)
		{
			$username = $this->tank_auth->get_username() ? $this->tank_auth->get_username() : 'creature';

			//Show cheat code hint after some seconds for hesistant buyers
			$params['timeout'] = $timeout;
			$params['title'] = "$username, Anything Wrong?";
			$params['body'] = " Allow us to make it right. Apply this konami cheat code and the world around you will burn with jealousy, seeing you with this geeky awesomeness and yes you can thank us later.<br><br> <strong>uuddlrlrba</strong> <br><br>Happy gaming/debugging!" ;

			notify_event('show_cheat_code', $params);
		}
	}

	//Should be called after an update or removal of an item from the cart
	function _reconfirm_cheat_code()
	{
		//If discount is applied, do a conditional-check again
		if($this->cart->is_discount_applied())
		{
			$coupon = $this->cart->discount_info();

			// if($this->_can_apply_code($coupon) == false) // closed on 01.07.2021
			if(_can_apply_code($coupon) == false) // dev on 01.07.2021
			{
				$this->cart->remove_discount();
			}
		}
	}

	function _show_auto_disc_notifs($comment)
	{
		$num_items = $this->cart->total_items();

		switch ($num_items)
		{
			case 1:
				//5% off
			show_alert($comment. " Btw, adding another item to your cart will give an instant 5% off");
			break;
			case 2:
				//5% off
			show_alert($comment. " See, magic happens, now add another item for a 10% off.");
			break;
			
			default:
			show_alert($comment);
			break;
		}		

	}

	function _apply_auto_disc()
	{
		//Check if there are x no. of products in the cart, apply an automated y% discount.
		$num_items = $this->cart->total_items();
		$auto_disc_info = null;

		foreach ($this->auto_disc_array as $cart_items => $disc_info)
		{
			if($num_items >= $cart_items)
			{
				$auto_disc_info = $disc_info;
			}
		}

		$applied_disc = $this->cart->discount_info();

		//auto discount should be more than the applied disc (if any)
		if($applied_disc['percentage'] < $auto_disc_info['percentage'])
		{
			// $this->_apply_discount( $auto_disc_info['coupon'] ); // closed on 01.07.2021
			_apply_discount($auto_disc_info['coupon']); // dev on 01.07.2021
		}		
	}

	function instant_checkout($product_id)
	{
		//Get the product using id
		$product = $this->database->getProductbyId($product_id);

		if($product)
		{
			$this->_add_to_cart($product);
		}
		
		redirect('checkout/');
	}

	function _add_to_cart($product)
	{
		$cart_item = array
		(
			'id' 	=> $product['product_id'],
			'qty'	=> '1',
			'price' => $product['product_price'],
			'name'  => $product['product_name'],
			'type'	=> $product['product_type'],
		);

		$extra = urldecode($this->input->post('extra'));
		if($extra)
		{
			$cart_item['options']['extra'] = $extra;
		}

		$row_id = $this->cart->insert($cart_item);

	}

	function add($product_id)
	{
		//Get the product using id
		$product = $this->database->getProductbyId($product_id);

		if($product)
		{
			$this->_add_to_cart($product);
		}
		
		$this->_show_add_to_cart_comment($product['add_to_cart_comment']);
		
		redirect('cart');
	}

	function _show_add_to_cart_comment($comment)
	{
		$this->config->item('auto_discounts') == true ? $this->_show_auto_disc_notifs($comment) : show_alert($comment);
	}

	function remove($row_id)
	{		
		$data = array('rowid' => $row_id, 'qty' => 0);
		$this->cart->update($data);
		$this->_reconfirm_cheat_code();

		// dev on 29.06.2021
		// update checkout_items table if that product is removed
		if(!empty($_GET['product_id'])) {
			$txn_id = $this->session->userdata('txn_id');
			$this->database->RemoveCheckoutSingleItemForTxnId($txn_id, $_GET['product_id']);
		}
		// End: dev on 29.06.2021



		redirect('cart');
	}



	function update()
	{
		foreach ($this->cart->contents() as $items)
		{
			if( $this->input->post($items['rowid']) != (string)FALSE)
			{
				$id = $items['rowid'];
				$quant = (int)$this->input->post($items['rowid']);

				//Update Cart
				$data = array('rowid' => $id, 'qty' => $quant);
				$this->cart->update($data);
			}
		}

		$this->_reconfirm_cheat_code();

		redirect('cart');
	}

	function _getDiscount($coupon)
	{
		$discount = $this->database->GetDiscountCoupon($coupon);
		
		if(!empty($discount)) {
			
			if(count($discount) > 0)
			{
				//Make sure it hasnt expired yet
				if( strtotime($discount['expiry']) > strtotime(date("Y-m-d")) )
				{
					return $discount['how_much'];
				}
			}
		}
		return 0;
	}	


	function applyDiscount()
	{
		$coupon = strtolower( trim($this->input->post('coupon')) );
		
		if($coupon != (string)FALSE)
		{
			// $this->_apply_discount($coupon); // closed on 01.07.2021
			_apply_discount($coupon); // dev on 01.07.2021
		}

		redirect('cart');
	}



	function _apply_discount($coupon)
	{
		//Log coupon to see people apply
		$this->database->SaveCheatCode($coupon);
		$discount_percentage = 0;
		$coupon_info = $this->database->GetDiscountCoupon($coupon);

		//Run some conditional-check for code
		if($this->_can_apply_code($coupon_info))
		{
			$discount_percentage = $this->_getDiscount($coupon);
			$this->cart->apply_discount($coupon, $discount_percentage);
		}

		$this->_notify_discount_applied($discount_percentage, $coupon_info);

	}

	function _can_apply_code($coupon_info)
	{
		$check_result = false;
		$coupon = $coupon_info['coupon'];
		$use_limit = isset($coupon_info['use_limit']) ? $coupon_info['use_limit'] : null;
		$use_count = isset($coupon_info['use_count']) ? $coupon_info['use_count'] : null;

		$can_use = $use_limit ? ($use_count < $use_limit) : true;
		
		if($can_use == false)
		{
			//Use Limit over
			return false;
		}

		switch ($coupon)
		{			
			case 'psychoness10':
			case 'easter_egg':
			case 'easteregg':			
			case 'auto_disc_5':			
			case 'iddqdfrapp':
			case 'psychoness15':
				//Should be applied on purchase of 2 or 3 tshirts
			if($this->cart->total_items() > 1)
			{
				$check_result = true;
			}
			break;

			case 'p2psycho':
				//Check if there are minimum 2 posters in cart
			if($this->cart->total_items() > 1)
			{
				$check_result = true;
				foreach ($this->cart->contents() as $items)
				{					
					if($items['type'] != 'posters')
					{
						$check_result = false;
						break;
					}
				}
			}				
			break;

			case 'p3psycho':
				//Check if there are minimum 3 posters in cart
			if($this->cart->total_items() > 2)
			{
				$check_result = true;
				foreach ($this->cart->contents() as $items)
				{
					if($items['type'] != 'posters')
					{
						$check_result = false;
						break;
					}
				}
			}
			break;

			case 'auto_disc_10':
			case 'powerup':
				//Should be applied on purchase of 3 or more products
			if($this->cart->total_items() > 2)
			{
				$check_result = true;
			}
			break;				

			case 'godmode_psycho':
				//Should be applied on purchase of 4 or more tshirts
			if($this->cart->total_items() > 3)
			{
				$check_result = true;
			}
			break;

			default:
			$check_result = true;
			break;
		}

		return $check_result;
	}

	function _notify_discount_applied($discount_percentage, $coupon_info)
	{
		$username = $this->tank_auth->get_username() ? $this->tank_auth->get_username() : 'creature';
		$domain_discount = get_current_user_discount_domain_info();

		//Notify event for modal pop up
		if($discount_percentage == 0)
		{

			$params['title'] = "Uh Oh!";
			$params['type'] = "error";			

			$body = strlen($coupon_info['error_text']) > 0 ? $coupon_info['error_text'] : "<strong>$username</strong>, either there is no such cheat code like this or it cannot be applied right now.<br>Anyway, we strongly encourage playing games with no cheat codes applied.<br>But here is a hint just for you.<br><br><strong>Hint : Google \"What is the Konami code\"</strong>. ";

			$params['body'] = $body;
		}
		// else if(count($domain_discount))
		else if(!empty($domain_discount))
		{
			$params['title'] = $username;
			$params['type'] = "success";

			$params['body'] = "We already gave you <strong>{$domain_discount['how_much']}%</strong> off because you belong to the lands of <strong>{$domain_discount['domain']}</strong>. Now dont push us, we cannot afford to give you anymore discount, that would be unfair for our people. Hope you understand.";
		}
		else
		{
			$params['type'] = "success";
			//Personalised message depending on cheat code applied
			switch ($coupon_info['coupon'])
			{
				case 'frapp_mode':
				$params['title'] = "Cheat Code Applied $discount_percentage% off";
				$params['body'] = "<strong>$username</strong>, We all have been through student life and we all know how important discounts are, wish frapp was there in our times as well. Enjoy your <strong>$discount_percentage%</strong> discount. <br><br>Happy gaming/debugging!" ;
				break;
				
				case 'bin_mode':
				$params['title'] = "Cheat Code Applied $discount_percentage% off";
				$params['body'] = "Hello, <strong>earthling</strong>, a big thank you from BinBag and Psycho Store for being a responsible creature of earth. For all your good deeds we have applied <strong>$discount_percentage%</strong> discount just for you. <br><br>Happy gaming/debugging!" ;
				break;					
				
				default:
				$params['title'] = "Cheat Code Applied $discount_percentage% off";
				$params['body'] = "<strong>$username</strong>, we strongly oppose gaming with cheat codes applied. But anyway, we have made this game <strong>$discount_percentage%</strong> easier, just for you.<br><br>Happy gaming/debugging!" ;
				break;
			}			
		}

		notify_event('apply_discount', $params);
	}
}
?>