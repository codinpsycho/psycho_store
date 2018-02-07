<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if(!function_exists('generate_header'))
{
	function generate_header(&$data)
	{
		$ci = &get_instance();
		$ci->load->model('database');
		$ci->load->library('tank_auth');
		$ci->load->library('cart');
		$ci->load->helper('url');

		//Login Info
		$data['user_id'] = 0;
		$data['user_name'] = null;

		if($ci->tank_auth->is_logged_in())
		{
			$data['user_id'] 	= $ci->tank_auth->get_user_id();
			$user_name 			= explode('@',$ci->tank_auth->get_username());
			$data['user_name'] 	= $user_name[0];
		}

		//Cart Info
		$data['num_items'] = $ci->cart->total_items();
		$data['total_price'] = $ci->cart->total();

		//Game search Links
		$data['supported_games'] = $ci->database->GetAllSuportedGames();

		//Meta tags
		$data['url'] = current_url();
		$data['favico'] = site_url($ci->config->item('favico'));
				
		generate_metainfo($data);

	}

}


if(!function_exists('generate_metainfo'))
{	
	function generate_metainfo(&$data)
	{
		$ci = &get_instance();

		if(isset($data['product']))
		{			
			//Title
			$data['title'] = $data['product']['product_game'].' '.$data['product']['category'].' '.$data['product']['product_type'].' India | '.$data['product']['product_name'];
			//Description			
			$data['description'] = $data['product']['product_intro'].' | Psycho Store';
			//Keywords
			$data['keywords'] = $ci->config->item('keywords').str_replace(' ', ', ', $data['product']['product_url']);

			$data['image'] = site_url($data['product']['product_image_path']);
		}
		else
		{		
			$data['title'] = $ci->config->item('title');
			$data['description'] = $ci->config->item('description');
			$data['keywords'] = $ci->config->item('keywords');
			$data['image'] = base_url($ci->config->item('favico'));
		}

		if(isset($data['meta_id']))
		{
			//Override Meta Title and description
			$meta_info = load_metainfo($data['meta_id']);			
			if(count($meta_info))
			{
				$data['title'] = $meta_info['title'];
				$data['description'] = $meta_info['description'];	
			}
			
		}
	}
}

if(!function_exists('get_metaid_by_name'))
{
	function get_metaid_by_name($name)
	{
		$ci =& get_instance();
		$ci->load->model('database');
		$meta_info = $ci->database->GetMetaInfoByName($name);

		$meta_id = 1; //General meta id for fail safe
		if(sizeof($meta_info))
		{
			$meta_id = $meta_info['metainfo_id'];
		}
		
		return $meta_id;		
	}
}

if(!function_exists('load_metainfo'))
{
	function load_metainfo($meta_id)
	{
		$ci =& get_instance();		
		$ci->load->model('database');
		$meta_info = $ci->database->GetMetaInfoById($meta_id);
		return $meta_info;		
	}
}

if(!function_exists('generate_product_table_for_email'))
{
	function generate_product_table_for_order($order_id)
	{
		$ci =& get_instance();

		$ci->load->library('table');
		$ci->load->model('database');
		$ci->table->set_heading('Name','Option','Qty','Price', 'Total');
		$final_total = 0;
		$order = $ci->database->GetOrderById($order_id);

		foreach ($order['order_items'] as $item)
		{
			$product = $item['product'];
			$name = $product['product_name'];
			$option = $item['option'];
			$qty = $item['count'];
			$price = $product['product_price'];
			$total = $price * $qty;
			$ci->table->add_row($name,$option,$qty,$price,$total );
			$final_total += $total;
		}
		
		$tmpl = array ( 'table_open'  => '<table border="1" cellpadding="5" cellspacing="0" >' );
		$ci->table->set_template($tmpl);

		$cell = array('data'=>'Sub Total :', 'colspan'=>4, 'align'=>'right');
		$ci->table->add_row($cell, $final_total );

		if($order['payment_mode'] == 'cod')
		{			
			$cell = array('data'=>'Cod Charges :', 'colspan'=>4, 'align'=>'right');
			$ci->table->add_row($cell,  $ci->config->item('cod_charge') );
			$final_total += $ci->config->item('cod_charge');
		}	

		$cell = array('data'=>'Discount :', 'colspan'=>4, 'class'=>'highlight', 'align'=>'right');
		$ci->table->add_row($cell, $final_total - $order['order_amount'] );

		$cell = array('data'=>'Shipping :', 'colspan'=>4, 'class'=>'highlight', 'align'=>'right');
		$ci->table->add_row($cell, 'Always Free' );

		$cell = array('data'=>'Final Price :', 'colspan'=>4, 'class'=>'highlight', 'align'=>'right');
		$ci->table->add_row($cell, $order['order_amount'] );

		return $ci->table->generate();
	}
}

if(!function_exists('format_address'))
{
	function format_address($address)
	{
		$complete_add = $address['first_name'].' '.$address['last_name'].'<br>'.$address['address_1'] .'<br>';
				if(isset($address['address_2']) &&  $address['address_2'] != NULL)
				{
					$complete_add = $complete_add.$address['address_2'].', ';
				}
				if(isset($address['address_3']) &&  $address['address_3'] != NULL)
				{
					$complete_add = $complete_add.$address['address_3'].', ';
				}				 	
				 $complete_add = $complete_add.$address['city'].' '.$address['pincode'].',<br>'.$address['state'].', '.$address['country'].'<br>'. $address['phone_number'];

		return $complete_add;
	}
}

function add_subscriber($email, $username = null)
{
	$ci = &get_instance();
	$ci->load->model('database');
	$ci->load->helper('mailgun_helper');

	$ci->database->Subscribe($email);
	mg_add_subscriber($email, $username);
}

if(!function_exists('product_url'))
{
	function product_url($product)
	{
		$id = $product['product_id'];
		$url = url_title($product['product_url']);
		$final_url = "product/"."$id/$url";
		return $final_url;
	}
}

function get_product_image($prod_id)
{
	$ci = &get_instance();
	$ci->load->helper('directory');
	$images = directory_map("images/product/$prod_id");	
		
	foreach ($images as $key => $img)
	{
		if(is_array($img) == false)
		{
			$final_images[] = "images/product/$prod_id/$img";
		}		
	}
	
	return $final_images;
}

function _add_address_and_user_to_orders(&$orders)
{
	$ci = &get_instance();
	$ci->load->model('database');

	//Get user details and address in the array
	foreach ($orders as $key => $value)
	{
		$orders[$key]['user'] = $ci->database->GetUserById($value['user_id']);
		$orders[$key]['address'] = $ci->database->GetAddressById($value['address_id']);
	}

	return $orders;	
}

if(!function_exists('always_refresh'))
{
	function always_refresh()
	{
		header("Cache-Control: no-store, no-cache, must-revalidate"); 
 		header("Cache-Control: post-check=0, pre-check=0", false);
 	}
}

if(!function_exists('try_domain_discount'))
{
	function check_domain_discount()
	{
		$ci = &get_instance();
		$ci->load->model('database');
		$ci->load->library('tank_auth');
		$ci->load->library('cart');

		$user = $ci->database->GetUserById($ci->tank_auth->get_user_id());		
		if(count($user))
		{
			$user_email = $user['email'];
			$email_info = explode('@', $user_email);
			$domain = $email_info[1];
			$discount_domain = $ci->database->GetDiscountDomain($domain);
			
			if(count($discount_domain))
			{
				$ci->cart->apply_discount($discount_domain['how_much']);
			}
		}
 	}
}

if(!function_exists('get_current_user_discount_domain_info'))
{
	function get_current_user_discount_domain_info()
	{		
		$ci = &get_instance();
		$ci->load->model('database');
		$ci->load->library('tank_auth');		
		$discount_domain = null;
		$user = $ci->database->GetUserById($ci->tank_auth->get_user_id());		
		if(count($user))
		{
			$user_email = $user['email'];
			$email_info = explode('@', $user_email);
			$domain = $email_info[1];
			$discount_domain = $ci->database->GetDiscountDomain($domain);
		}

		return $discount_domain;
	}
}

if(!function_exists('notify_event'))
{
	function notify_event($event_name, $params = null)
	{
		$ci = &get_instance();
		$ci->load->library('session');
		$events = $ci->session->userdata('events');
		$events[$event_name] = $params;
		$ci->session->set_userdata('events', $events);
 	}
}

if(!function_exists('execute_events'))
{
	function execute_events(&$data)
	{
		$ci = &get_instance();
		$ci->load->library('session');
		$events = $ci->session->userdata('events');
		if($events)
		{
			foreach ($events as $event_name => $params)
			{
				$script_params['timeout'] = 0;
				$script_params['button_text'] = 'Close';
				$script_params['event_name'] = $event_name;
				switch ($event_name)
				{
					case 'login_done':
						$discount_domain = get_current_user_discount_domain_info();
						if(count($discount_domain))
						{
							$domain = $discount_domain['domain'];
							$discount = $discount_domain['how_much'];
							$script_params['modal_title'] = $params['title'];
							$script_params['modal_body']  = "We noticed that you hail from the lands of <strong>$domain.</strong> We have huge respect for creatures hailing from that land, because of which we will be giving you <strong>$discount%</strong> off on each and every purchase that you make from us.";
							$data['scripts'][] = array('path' => 'events/modal', 'params' => $script_params);
						}
						break;

					case 'apply_discount':
						$script_params['title'] = $params['title'];
						$script_params['body']  = $params['body'];
						$script_params['type']  = $params['type'];
						$data['scripts'][] = array('path' => 'events/sweetalert', 'params' => $script_params);
						break;
					
					case 'show_cheat_code':
						$script_params['title'] = $params['title'];
						$script_params['body']  = $params['body'];
						$script_params['type']  = 'info';
						$script_params['timeout'] = $params['timeout'];
						$script_params['button_text'] = 'Thanks, you guys rock';
						$data['scripts'][] = array('path' => 'events/sweetalert', 'params' => $script_params);
						break;

					case 'register_cart':
						$script_params['modal_title'] = $params['title'];
						$script_params['modal_body']  = $params['body'];
						$data['scripts'][] = array('path' => 'events/modal', 'params' => $script_params);
						break;

					case 'alert':
						$script_params['alert_text'] = $params['alert_text'];
						$script_params['timeout'] = $params['timeout'];
						$data['scripts'][] = array('path' => 'events/alert', 'params' => $script_params);
						break;
					
					case 'instafeed':
						$script_params['tag_name'] = $params['tag_name'];						
						$data['scripts'][] = array('path' => 'events/instafeed', 'params' => $script_params);
						break;

					case 'geolocation':
						$data['scripts'][] = array('path' => 'events/geolocation', 'params' => null);
						break;

					default:
						# code...
						break;
				}
			}
			$ci->session->set_userdata('events', null);
		}
 	}
}

function show_alert($text, $timeout = 0)
{
	$params['alert_text'] = $text;
	$params['timeout'] = $timeout;

	notify_event('alert', $params);
}

if(!function_exists('display'))
{
	function display($page, $data)
	{
		$ci = &get_instance();
		$status = $ci->config->item('current_site_status');

		switch ($status)
		{
			case 'LIVE':
				_live($page, $data);
				break;
			
			case 'TRAVELLING':
				_travelling();
				break;
			
			case 'down':
				_down();
				break;

			default:
				# code...
				break;
		}
	}
}

// *** NOT TO BE CALLED FROM OUTSIDE *** //

function _live($page, $data)
{
	$ci = &get_instance();
	$ci->load->library('session');
	$ci->load->model('database');

	generate_header($data);	
		
	//Show header based on page
	$header = stristr($page, 'admin') ? $ci->load->view('admin/admin_header', $data, true) : $ci->load->view('header', $data, true);

	//Show body		
	switch ($page)
	{
		case 'search':
			$body = $ci->load->view('view_search', $data, true);
			break;
		case 'browse':
			$body = $ci->load->view('browse', $data, true);	
			break;
		case 'home':
			$body = $ci->load->view('home', $data, true);
			break;
		case 'product':	
			$body = $ci->load->view('view_product', $data, true);
			break;
		case 'feedback_wall':
			$body = $ci->load->view('feedback_wall', $data, true);
			break;
		case 'view_giveaway':
			$body = $ci->load->view('view_giveaway', $data, true);
			break;
		case 'pay':
			$body = $ci->load->view('view_pay', $data, true);
			break;			
		case 'contact':
			$body = $ci->load->view('view_contact', $data, true);
			break;
		case 'cart':
			$body = $ci->load->view('view_cart',$data, true);
			break;
		case 'login':
			$body = $ci->load->view('auth/login_form', $data, true);
			break;
		case 'post_login':
			$body = $ci->load->view('auth/surprise', $data, true);
			break;
		case 'register_user_address':
			$body = $ci->load->view('auth/register', $data, true);
			break;
		case 'forgot_password':
			$body = $ci->load->view('auth/forgot_password_form', $data, true);
			break;
		case 'add_address':
			$body = $ci->load->view('auth/add_address', $data, true);
			break;			
		case 'reset_password':
			$body = $ci->load->view('auth/reset_password_form', $data, true);
			break;
		case 'send_again':
			$body = $ci->load->view('auth/send_again_form', $data, true);
			break;
		case 'feedback_form':
			$body = $ci->load->view('auth/feedback_form', $data, true);
			break;
		case 'admin_orders':
			$body = $ci->load->view('admin/admin_orders', $data, true);
			break;
		case 'admin_products':
			$body = $ci->load->view('admin/admin_products', $data, true);
			break;
		case 'admin_product_add_edit':
			$body = $ci->load->view('admin/product_add_edit', $data, true);
			break;
		case 'admin_feedback':
			$body = $ci->load->view('admin/admin_feedbacks', $data, true);
			break;
		case 'admin_mail':
			$body = $ci->load->view('admin/admin_mails', $data, true);
			break;
		case 'admin_shipments':
			$body = $ci->load->view('admin/admin_shipments', $data, true);
			break;
		case 'admin_logistics':
			$body = $ci->load->view('admin/admin_logistics', $data, true);
			break;
		case 'admin_users':
			$body = $ci->load->view('admin/admin_users', $data, true);
			break;
		case 'admin_discounts':
			$body = $ci->load->view('admin/admin_discounts', $data, true);
			break;
		case 'admin_send_mail':
			$body = $ci->load->view('admin/admin_send_mail', $data, true);
			break;
		case 'admin_checkouts':
			$body = $ci->load->view('admin/admin_checkouts', $data, true);
			break;
		case 'partner':
			$body = $ci->load->view('partner_view', $data, true);
			break;
		case 'address':
			$body = $ci->load->view('view_address', $data, true);
			break;
		case 'review':
			$body = $ci->load->view('view_review_order', $data, true);
			break;
		case 'message':
		case 'basic':
			$body = $ci->load->view('basic_view', $data, true);
			break;
		case 'insights':
			$body = $ci->load->view('view_insights', $data, true);
			break;
		case 'offers':
			$body = $ci->load->view('view_offers', $data, true);
			break;
		case 'sales':
			$body = $ci->load->view('view_sales', $data, true);
			break;			
		default:
			show_404();
		break;
	}
	
	execute_events($data);

	$data['custom_events'] = $ci->load->view('custom_events', $data, true);
	$data['external_scripts'] = $ci->load->view('external_scripts', null, true);
	$data['event_tracking'] = $ci->load->view('event_tracking', null, true);
	$data['intro_signature'] = $ci->load->view('intro_signature.html', null, true);

	$footer = $ci->load->view('footer', $data, true);

	$data['header'] = $header;
	$data['body'] = $body;
	$data['footer'] = $footer;

	$ci->load->view('main_view', $data);
}

function _travelling()
{
	$ci = &get_instance();
	$ci->load->library('session');
	$ci->load->model('database');

	$data['num_of_gamers'] = $ci->database->GetNumOfSubscribers();
	$ci->load->view('view_travelling', $data);
}

function _validate_user()
{
	$ci = &get_instance();
	$ci->load->library('tank_auth');	
	$ci->load->model('database');

	$current_user = $ci->database->GetUserById($ci->tank_auth->get_user_id());
	$valid_user = false;
	$admin_emails = $ci->config->item('admin_email');

	foreach ($admin_emails as $key => $email)
	{
		if($current_user)
		{
			if( $current_user['email'] == $email )
			{
				$valid_user = true;
			}
		}
	}

	if($valid_user == false)
	{
		redirect('');
	}
}

?>