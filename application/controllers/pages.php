<?php 
/**
* 	
*/
class Pages extends CI_controller
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
	}

	function index()
	{
		$this->home();
	}

	function home()
	{
		$this->show_featured_prods();
	}

	function launch_signup()
	{
		$email_id = strtolower($this->input->post('subscribe_email'));
		$data = array();

		if(valid_email($email_id))
		{
			$this->database->Subscribe($email_id);
			$data['site_name'] = 'Psycho Store';
			$params = mg_create_mail_params('subscribe', $data);
			mg_send_mail($email_id, $params);
		}		

		redirect('');
	}	

	function GenerateSuggestions($product, $howmany)
	{
		$exception[] = $product;
		$suggested_products = $this->database->GetRandomProducts($howmany,'all', 'all', $exception);
		return $suggested_products;
	}

	function GetNextPreviousIds($current_id, &$next, &$prev, $total_products)
	{		
		$result = array();
		$id = $current_id;
		
		while(count($result) < 1)
		{
			$id = $id + 1;
			if($id > $total_products )
				$id = 1;
			$result = $this->database->GetProductById($id);						
		}

		$next = $result['product_id'];		
		
		//reset
		$result = null;
		$id = $current_id;
		while(count($result) < 1)
		{
			$id = $id - 1;
			if($id < 1 )
				$id = $total_products;
			$result = $this->database->GetProductById($id);
		}
		$prev = $result['product_id'];	
	}

	function AddToRecentlyViewed($product)
	{
		$recently_viewed = $this->session->userdata('recently_viewed');
				
		//Make sure no duplicate entries are there
		if(is_array($recently_viewed))
		{
			foreach ($recently_viewed as $key => $value)
			{
				if($value['product_id'] == $product['product_id'])
					return;
			}
		}
		
		//Make sure at a time there are only 6 recent prods
		if(count($recently_viewed) >= 6)
		{
			$recently_viewed = array_reverse($recently_viewed);
			array_pop($recently_viewed);
			$recently_viewed = array_reverse($recently_viewed);
		}

		$recently_viewed[] = $product;		
		$this->session->set_userdata('recently_viewed', $recently_viewed);
	}

	function GetRecentlyViewed()
	{
		return $this->session->userdata('recently_viewed');
	}

	function giveaways()
	{
		display('view_giveaway', null);
	}

	function feedback()
	{
		$feedback = $this->database->GetFeedback(TRUE);

		//This is required to show what the user bought
		foreach ($feedback as $key => $value)
		{
			$user = (array)$this->users->get_user_by_email($value['email']);
			if($user)
			{
				$orders = $this->database->GetOrdersForUser($user['id'], true);
				if($orders)
				{
					//We will show the latest order
					$latest_order_num = count($orders) - 1;
					$order_items = $orders[$latest_order_num]['order_items'];

					foreach ($order_items as $item_key => $item)
					{
						$feedback[$key]['products'][] = array('product_name' => $item['product']['product_name'], 'product_url' => product_url($item['product']) );
					}
				}				
			}
		}		

		$feedback = array_reverse($feedback);
		
		$data['feedbacks'] = $feedback;
		
		display('feedback_wall', $data);
	}

	function explore($url = null, $sorting = 'latest')
	{
		$data['base_url'] = "explore/$url";
		switch ($url)
		{
			case 'gaming-anime-geek-t-shirts-india':
				$data['prod_1_url'] = 'explore/gaming-anime-geek-posters-india';
				$data['prod_2_url'] = 'explore/gaming-anime-geek-coffee-mugs-india';
				$data['prod_1_title'] = 'Posters';
				$data['prod_2_title'] = 'Coffee Mugs';
				$data['header_title'] = "T-Shirts";
				$this->_browse($data, 'tshirt', $sorting);
				break;
			
			case 'gaming-anime-geek-posters-india':
				$data['prod_1_url'] = 'explore/gaming-anime-geek-t-shirts-india';
				$data['prod_2_url'] = 'explore/gaming-anime-geek-coffee-mugs-india';
				$data['prod_1_title'] = 'Tees';
				$data['prod_2_title'] = 'Coffee Mugs';
				$data['header_title'] = "Posters";
				$this->_browse($data, 'posters', $sorting);
				break;

			case 'gaming-anime-geek-coffee-mugs-india':
				$data['prod_1_url'] = 'explore/gaming-anime-geek-t-shirts-india';
				$data['prod_2_url'] = 'explore/gaming-anime-geek-posters-india';
				$data['prod_1_title'] = 'Tees';
				$data['prod_2_title'] = 'Poster';
				$data['header_title'] = "Coffee Mugs";
				$this->_browse($data, 'mugs', $sorting);
				break;

			default:
				# code...
				break;
		}
	}

	function _browse($data, $prod_type, $sorting, $game_name = 'all')
	{
		$data['products'] = $this->database->GetProducts($prod_type, $sorting, $game_name);
		if($sorting == 'latest')
		{
			$data['latest_link_state'] = 'active';
			$data['popular_link_state'] = 'disabled';			
		}
		else if($sorting == 'popular')
		{
			$data['latest_link_state'] = 'disabled';
			$data['popular_link_state'] = 'active';
		}
	
		$params['tag_name'] = 'psychofamous';
		notify_event('instafeed', $params);

		display('browse', $data);

	}

	function _get_other_products_for_design($product)
	{
		$other_types_of_prods = $this->database->GetSupportedProductsForDesign($product['design_id']);

		foreach ($other_types_of_prods as $key => $prod)
		{
			if($prod['product_type'] == $product['product_type'])
				unset($other_types_of_prods[$key]);
		}

		return $other_types_of_prods;
	}


	function product($id, $url = null)
	{
		$total_products = $this->database->GetMaxProductID();
		$url = $this->beautify($url,'_');
		$result = $this->database->GetProductById($id);		

		if($result)
		{
			$next = $prev = 0;
			$this->GetNextPreviousIds($result['product_id'], $next, $prev, $total_products);
			$data['product'] = $result;
			$data['total_products'] = $total_products;
			$data['product_state'] = $result['product_state'];
			$data['next_id'] = product_url( $this->database->GetProductById($next) );
			$data['prev_id'] = product_url( $this->database->GetProductById($prev) );
			$data['size_chart'] = site_url($this->config->item('size_chart'));
			$data['images'] = get_product_image($result['product_id']);
			$data['hashtag'] = $result['hashtag'];
			$data['restock_date'] = $this->config->item('restock_date');
			$other_types_of_prods = $this->_get_other_products_for_design($result);
			$data['other_prod_types'] = $other_types_of_prods;
			
			$this->_generate_product_specific_views($result, $data);

			$params['tag_name'] = $data['hashtag'];
			notify_event('instafeed', $params);

			//Generate Suggestions
			$data['suggested_products'] = $this->GenerateSuggestions($result, 5);

			$data['recently_viewed'] = $this->GetRecentlyViewed();
			$this->AddToRecentlyViewed($result);

			display('product', $data);
		}
		else
		{
			$data['heading'] = 'No Products Found';
			$data['content'] = "I am sure, this has something to do with G-Man, anyways just go somewhere else, try some other product";
			display('basic', $data);
		}
	}

	function _generate_product_specific_views($product, &$data)
	{
		$this->_setup_stock_info($product, $data);

		$data['img_alt'] = $product['product_intro'];

		switch ($product['product_type'])
		{
			case 'hoodies':
			case 'tshirt':				
				$data['product_img_view'] = $this->load->view('view_product_image', $data, true);
				$data['details_view'] = $this->load->view('view_tshirt_option_details', $data, true);
				break;

			case 'mobilecover':
				$data['product_img_view'] = $this->load->view('view_mobile_cover_image', $data, true);
				$data['details_view'] = $this->load->view('view_mobile_cover_option_details', $data, true);
				break;
			
			case'posters':
			case 'mugs':
				$data['product_img_view'] = $this->load->view('view_product_image', $data, true);
				$data['details_view'] = $this->load->view('view_product_no_option_details', $data, true);
				break;

			default:
				# code...
				break;
		}
	}

	function _setup_stock_info($product, &$data)
	{
		switch ($product['product_type'])
		{
			case 'hoodies':
			case 'tshirt':
				$this->_setup_tshirt_options_info($product, $data);
				break;
			
			case 'mugs':
				$this->_setup_mugs_stock_info($product, $data);
				break;
			
			case 'mobilecover':
				$this->_setup_mobilecovers_options_info($product, $data);
				break;

			default:
				# code...
				break;
		}
	}

	function _setup_mugs_stock_info($product, &$data)
	{
		//nothing for now
	}

	function _setup_mobilecovers_options_info($product, &$data)
	{
		$data['supported_models'] = $this->database->GetSupportedMobileModels();
	}


	function _setup_tshirt_options_info($product, &$data)
	{
		$data['small_stock']="";
		$data['medium_stock']="";
		$data['large_stock']="";
		$data['xl_stock']="";

		$prod_details = $product['product_details'];

		$data['show_size_preorder_info'] = false;

		if($prod_details['small_qty'] <= 0)
		{
			$data['show_size_preorder_info'] = $prod_details['size_preorder'] ? TRUE : false;
			$data['small_stock'] = $prod_details['size_preorder'] ? "preorder" : 'disabled';
		}
		if($prod_details['medium_qty'] <= 0)
		{
			$data['show_size_preorder_info'] = $prod_details['size_preorder'] ? TRUE : false;
			$data['medium_stock'] = $prod_details['size_preorder'] ? "preorder" : 'disabled';
		}
		if($prod_details['large_qty'] <= 0)
		{
			$data['show_size_preorder_info'] = $prod_details['size_preorder'] ? TRUE : false;
			$data['large_stock'] = $prod_details['size_preorder'] ? "preorder" : 'disabled';
		}
		if($prod_details['xl_qty'] <= 0)
		{
			$data['show_size_preorder_info'] = $prod_details['size_preorder'] ? TRUE : false;
			$data['xl_stock'] = $prod_details['size_preorder'] ? "preorder" : 'disabled';
		}
	}

	function show_featured_prods()
	{
		//For now only tees, later on featured prods
		$data['products'] = $this->database->GetProducts('all', 'popular', 'all', true);
		$data['latest_link_state'] = 'active';
		$data['popular_link_state'] = 'none';
		$params['tag_name'] = 'psychofamous';
		notify_event('instafeed', $params);

		display('home', $data);
	}

	function latest()
	{
		$data['products'] = $this->database->GetProducts('all', 'latest', 'all');
		$data['latest_link_state'] = 'active';
		$data['popular_link_state'] = 'none';
		$params['tag_name'] = 'psychofamous';
		notify_event('instafeed', $params);

		display('home', $data);
	}

	function popular()
	{		
		$data['products'] = $this->database->GetProducts('all', 'popular', 'all');
		$data['popular_link_state'] = 'active';
		$data['latest_link_state'] = 'none';
		$params['tag_name'] = 'psychofamous';
		notify_event('instafeed', $params);

		display('home', $data);
	}
	
	//Removes spaces from a url
	function beautify($string, $replace_char)
	{
		return str_replace($replace_char,' ',$string);
	}


	function like($like_what = "")
	{
		$name = ($this->input->post('search_query') != false) ? trim($this->input->post('search_query')) : $this->beautify($like_what,'-');

		$data['search_result'] = 0;
		$data['search_text'] = $name;
		$data['products'] = array();
		if(strlen($name))
		{
			$result = $this->database->GetProducts('all','popular', $name);
			$count = count($result);
			$data['search_result'] = $count;
			
			if($result)
				$data['products'] = $result;			
		}		

		display('search', $data);
	}

	function subscribe()
	{
		$email_id = strtolower($this->input->post('subscribe_email'));
		$data = array();

		if(valid_email($email_id))
		{			
			if($this->database->Subscribe($email_id))
			{
				$data['site_name'] = 'Psycho Store';
				mg_add_subscriber($email_id);
				$params = mg_create_mail_params('subscribe', $data);
				mg_send_mail($email_id, $params);

				$data['heading'] = "<small>Greetings</small> ".$email_id;
				$data['content'] = "We dont know who you are. We dont know what you want. If you are looking for toilet brushes, We can tell you we dont have any. But what we do have are a very particular set of gaming stuff. Stuff that we have made with a lot of hardwork. Stuff that can make people like you very happy. If you buy that stuff from us, that will be the end of it. We will not look for you, We will not pursue you. But if you dont, we will look for you, we will find you, and we will keep updating you.";
			}
			else
			{
				$data['heading'] = $email_id;
				$data['content'] = "We understand you love us, and you love playing around our website and subscribing to Psycho Store newsletter. But you are already in our list you know. Dont fret we wont forget you, you know. Adding your name once is enough you know. Just so you know.";
			}
		}
		else
		{
			$data['email_id'] = $email_id;
			$data['heading'] = "Damn, you cant even type an email correctly";
			$data['content'] = "Just dont disappoint this time, try again";
		}

		display('basic', $data);
	}

	function unsubscribe()
	{
		$email_id = strtolower($this->input->post('subscribe_email'));
		$data = array();

		if(valid_email($email_id))
		{
			$this->database->Unsubscribe($email_id);
			mg_unsubscribe($email_id);
			$data['email_id'] = $email_id;
			$data['heading'] = "You have been Unsubscribed";
			$data['content'] = "So this is the end of us. Take care and stay Psycho anyways.";
		}

		display('basic', $data);
	}

	function shipping_returns()
	{
		$data['heading'] = "Shipping and Returns";
		$data['ret_address'] = format_address($this->config->item('return_address'));
		$data['content'] = $this->load->view('view_shipping', $data, true);
		display('basic', $data);
	}

	function coupon_partners()
	{
		$data['heading'] = "Coupon Partners";		
		$data['content'] = $this->load->view('view_coupon_partners', $data, true);
		display('basic', $data);	
	}

	function contact()
	{
		$data['return_address'] = format_address($this->config->item('return_address'));
		display('contact', $data);
	}

	function media()
	{
		$data['heading'] = "Who's talking about us";
		$data['content'] = $this->load->view('view_media', null, true);
		display('basic', $data);
	}

	function about()
	{
		$data['heading'] = "Who are We";
		$data['content'] = $this->load->view('view_about', null, true);
		display('basic', $data);
	}
}
?>