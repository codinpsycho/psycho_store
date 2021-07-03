<?php (defined('BASEPATH')) OR exit('No direct script access allowed');


function _apply_discount($coupon)
{
	$ci = &get_instance();
	$ci->load->model('database');

	//Log coupon to see people apply
	$ci->database->SaveCheatCode($coupon);
	$discount_percentage = 0;
	$coupon_info = $ci->database->GetDiscountCoupon($coupon);

		//Run some conditional-check for code
	if(_can_apply_code($coupon_info))
	{
		$discount_percentage = _getDiscount($coupon);
		$ci->cart->apply_discount($coupon, $discount_percentage);
	}

	_notify_discount_applied($discount_percentage, $coupon_info);

}


function _getDiscount($coupon)
{
	$ci = &get_instance();
	$ci->load->model('database');
	$discount = $ci->database->GetDiscountCoupon($coupon);

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


function _can_apply_code($coupon_info)
{
	$ci = &get_instance();
	$ci->load->library('cart');

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
		if($ci->cart->total_items() > 1)
		{
			$check_result = true;
		}
		break;

		case 'p2psycho':
				//Check if there are minimum 2 posters in cart
		if($ci->cart->total_items() > 1)
		{
			$check_result = true;
			foreach ($ci->cart->contents() as $items)
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
		if($ci->cart->total_items() > 2)
		{
			$check_result = true;
			foreach ($ci->cart->contents() as $items)
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
		if($ci->cart->total_items() > 2)
		{
			$check_result = true;
		}
		break;				

		case 'godmode_psycho':
				//Should be applied on purchase of 4 or more tshirts
		if($ci->cart->total_items() > 3)
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
	$ci = &get_instance();
	$ci->load->library('tank_auth');

	$username = $ci->tank_auth->get_username() ? $ci->tank_auth->get_username() : 'creature';
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

			// case added on 01.07.2021
			case 'review_discount':	
			$params['title'] = "First Order Welcome Discount";
			$params['body'] = "<strong>$username</strong>, as a welcome gesture to the psycho family, here is a <strong>$discount_percentage%</strong> off on your first loot.<br><br>Satisfy your inner geek and dont forget to tag us on instagram." ;
			break;
			// case end

			default:
			$params['title'] = "Cheat Code Applied $discount_percentage% off";
			$params['body'] = "<strong>$username</strong>, we strongly oppose gaming with cheat codes applied. But anyway, we have made this game <strong>$discount_percentage%</strong> easier, just for you.<br><br>Happy gaming/debugging!" ;
			break;
		}			
	}

	notify_event('apply_discount', $params);
}

?>