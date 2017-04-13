<?php 
/**
* 
*/
class cart extends CI_controller
{	
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('cart');
		$this->load->model('database');
		$this->load->helper('url');
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
		foreach ($this->cart->contents() as $item)
		{
			$prod_id = $item['id'];
			$product = $this->database->GetProductById($prod_id);

			//Check stock and set stock info
			$data['products'][$item['rowid'].'stock_state'] = $this->_set_stock_state($product, $item);

			$data['products'][$prod_id] = $product;
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
			$params['body'] = " Allow us to make it right. Apply this konami cheat code and the world around you will burn with jealousy, seeing you with this geeky awesomeness and yes you can thank us later.<br><br> <strong>upupdowndownleftrightleftrightba</strong> <br><br>Happy gaming/debugging!" ;

			notify_event('show_cheat_code', $params);
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
					'source'=> $product['product_source'],
				);
						
		$extra = urldecode($this->input->post('extra'));
		if($extra)
		{
			$cart_item['options']['extra'] =  $extra;
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

		show_alert($product['add_to_cart_comment']);
		
		redirect('cart');
	}

	function remove($row_id)
	{		
		$data = array('rowid' => $row_id, 'qty' => 0);
		$this->cart->update($data);

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

		//If discount is applied, do a conditional-check again
		if($this->cart->is_discount_applied())
		{
			$coupon = $this->cart->get_applied_discount_coupon();
			if($this->_can_apply_code($coupon) == false)
			{
				$this->cart->remove_discount();
			}
		}		

		redirect('cart');
	}

	function _getDiscount($coupon)
	{
		$discount = $this->database->GetDiscountCoupon($coupon);
		
		if(count($discount) > 0)
		{
			//Make sure it hasnt expired yet
			if( strtotime($discount['expiry']) > strtotime(date("Y-m-d")) )
			{
				return $discount['how_much'];
			}
		}
		return 0;
	}	

	function applyDiscount()
	{
		$coupon = strtolower( trim($this->input->post('coupon')) );
		
		if($coupon != (string)FALSE)
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

			$this->_notify_discount_applied($discount_percentage, $coupon);
		}

		redirect('cart');
	}

	function _can_apply_code($coupon_info)
	{
		$check_result = false;
		$coupon = $coupon_info['coupon'];
		$use_limit = $coupon_info['use_limit'];
		$use_count = $coupon_info['use_count'];

		$can_use = $use_limit ? ($use_count < $use_limit) : true;
		
		if($can_use == false)
		{
			//Use Limit over
			return false;
		}

		switch ($coupon)
		{			
			case 'psychoness10':
				//Should be applied on purchase of only one tshirt
				if($this->cart->total_items() >= 1)
				{
					$check_result = true;
				}
				break;

			case 'psychoness15':
				//Should be applied on purchase of 2 or 3 tshirts
				if($this->cart->total_items() > 1)
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

	function _notify_discount_applied($discount_percentage, $coupon)
	{
		$username = $this->tank_auth->get_username() ? $this->tank_auth->get_username() : 'creature';
		$domain_discount = get_current_user_discount_domain_info();

		//Notify event for modal pop up
		if($discount_percentage == 0)
		{
			$params['title'] = "wrong cheat code";
			$params['type'] = "error";

			$params['body'] = "<strong>$username</strong>, either there is no such cheat code like this or it cannot be applied right now.<br>Anyway, we strongly encourage playing games with no cheat codes applied.<br>But here is a hint just for you.<br><br><strong>Hint : Google \"What is the Konami code\"</strong>. " ;
		}
		else if(count($domain_discount))
		{
			$params['title'] = $username;
			$params['type'] = "success";

			$params['body'] = "We already gave you <strong>{$domain_discount['how_much']}%</strong> off because you belong to the lands of <strong>{$domain_discount['domain']}</strong>. Now dont push us, we cannot afford to give you anymore discount, that would be unfair for our people. Hope you understand.";
		}
		else
		{
			$params['type'] = "success";
			//Personalised message depending on cheat code applied
			switch ($coupon)
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