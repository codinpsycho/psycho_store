<?php 

class OrderState 
{
	const Pending = 'pending';
	const Packaging = 'packaging';
	const Shipped = 'shipped';
	const Requested = 'requested';
	const Returned = 'returned';
}

class admin extends CI_controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('cart');
		$this->load->library('tank_auth');
		$this->load->library('table');
		$this->load->library('form_validation');
		$this->load->model('database');
		$this->load->helper('url');
		$this->load->helper('html');
		$this->load->helper('form');
		$this->load->helper('psycho_helper');
		$this->load->helper('mailgun_helper');
		$this->load->helper('shipping_helper');		
		$this->load->library('session');
		$this->config->load('shipping_settings');
	}

	function index()
	{		
		$this->orders();
	}

	function _validate_user()
	{
		$current_user = $this->database->GetUserById($this->tank_auth->get_user_id());
		$valid_user = false;
		$admin_emails = $this->config->item('admin_email');

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

	function labels($waybill)
	{
		$this->_validate_user();

		$waybill = trim($waybill);
		$data = null;
		
		$requested_shipments = $this->database->GetOrdersByState(OrderState::Requested);
		$this->_add_address_and_user_to_orders($requested_shipments);
		
		//Get this particular shipment
		$required_shipment = null;
		foreach ($requested_shipments as $key => $shipment)
		{
			if($waybill == $shipment['waybill'])
			{
				$required_shipment = $shipment;
				break;
			}
		}
		
		$label = create_shipping_label($waybill);
		$label = $label['packages'][0];
		$shipping_details = $this->database->GetShippingDetails($shipment['address']['pincode']);

		$data['company_logo'] = site_url($this->config->item('company_logo'));
		$data['courier_logo'] = site_url($this->config->item('delhivery_logo'));
		$data['wb_barcode'] = $label['barcode'];
		$data['oid_barcode'] = $label['oid_barcode'];
		$data['address'] = format_address($shipment['address']);
		$data['city'] = $shipment['address']['city'];
		$data['pin'] = $shipment['address']['pincode'];
		$data['coc_code'] = $shipping_details['coc_code'];
		$data['dispatch_center'] = $shipping_details['dispatch_center'];
		$data['payment_mode'] = strtoupper($shipment['payment_mode']);		
		$data['return_address'] = format_address($this->config->item('return_address'));
		$data['order_amount'] = $shipment['order_amount'];

		$this->load->view('admin/label', $data);
	}

	function logistics()
	{
		$this->_validate_user();

		$data['delhivery_waybills'] = $this->database->NumWaybills();
		$data['delhivery_pincodes'] = $this->database->NumPincodes();

		$this->_check_for_new_waybills();

		display('admin_logistics', $data);
	}

	function _check_for_new_waybills()
	{
		//Are we fetching new waybills
		$log_partner = $this->input->post('logistic_partner');
		$num_waybills = $this->input->post('num_waybills');
		$redirect = false;	//To refresh the count
		
		if($log_partner != false && $num_waybills != false)
		{
			switch ($log_partner)
			{
				case 'delhivery':
					$delhivery_waybills = fetch_delhivery_waybills($num_waybills);
					$this->_insert_new_waybills($delhivery_waybills);
					$redirect = true;
					break;
				
				default:
					# code...
					break;
			}
		}

		if($redirect)
		{
			redirect('admin/logistics');
		}
	}

	function _insert_new_waybills($waybills)
	{
		//Package waybills first
		$waybills = explode(',', $waybills);

		foreach ($waybills as $key => $waybill)
		{			
			$delhivery_waybills[$key]['waybill'] = $waybill;
		}

		$this->database->InsertWaybills($delhivery_waybills);
	}

	function users($id = null)
	{
		$this->_validate_user();
				
		if(is_null($id) == false)
		{
			$user = $this->database->GetUserById($id);			
			$points = $this->input->post('points');
			if($points != false)
			{
				$points = $points > 500 ? 500 : $points;
				$points += $user['points'];
				$this->database->RewardUser($id, $points);
			}
			redirect('admin/users');
		}

		$all_users = $this->database->GetAllUsers();
		$data['num_users'] = count($all_users);
		$data['users_table'] = $this->_generate_users_table($all_users);

		display('admin_users', $data);
	}

	function discounts($id = null)
	{
		$this->_validate_user();

		/*Two type of discounts : 
		1. Domain discounts
		2. Discount Coupons
		*/

		$discount_domains = $this->database->GetDiscountDomain();
		$discount_coupons = $this->database->GetDiscountCoupon();
		$discounts['domains'] = $discount_domains;
		$discounts['coupons'] = $discount_coupons;

		$discount_table = $this->_generate_discount_table($discounts);

		$data['discount_table'] = $discount_table;
		$data['num_domains'] = count($discount_domains);
		$data['num_coupons'] = count($discount_coupons);
		display('admin_discounts', $data);
	}

	function add_discount()
	{
		$discount_type = $this->input->post('discount_type');
		if($discount_type)
		{
			$name = trim($this->input->post('discount_name'));
			$discount_percentage = trim($this->input->post('discount_percentage'));

			switch ($discount_type)
			{
				case 'coupon':
					$expiry_date = $this->input->post('expiry_date');
					if($name != false && $discount_percentage != false && $expiry_date != false)
					{
						$coupon['coupon'] = $name;
						$coupon['how_much'] = $discount_percentage;
						$coupon['expiry'] = $expiry_date;
						$this->database->AddDiscountCoupon($coupon);
					}
					break;
				case 'domain':
					if($name != false)
					{
						$domain_info['domain'] = $name;
						if($discount_percentage != false)
						{
							$domain_info['how_much'] = $discount_percentage;
						}

						$this->database->AddDiscountDomain($domain_info );
					}
					break;			
				default:
					# code...
					break;
			}
		}

		

		redirect('admin/discounts');
	}

	function remove_discount($name, $type)
	{
		$name = trim($name);
		switch ($type)
		{
			case 'domain':
				$this->database->RemoveDiscountDomain($name);
				break;

			case 'coupon':
				$this->database->RemoveDiscountCoupon($name);
				break;

			default:
				# code...
				break;
		}		

		redirect('admin/discounts');
	}

	function update_discount_domain($domain)
	{
		$domain = trim($domain);
		$discount_percentage = $this->input->post('discount_percentage');
		if($discount_percentage != false)
		{
			$this->database->SetDiscountForDomain($domain, $discount_percentage);
		}

		redirect('admin/discounts');
	}	

	function _set_discount_for_domain($domain, $discount_percentage)
	{
		$this->database->SetDiscountForDomain($domain, $discount_percentage);

		redirect('admin/discount_domains');
	}

	function mails()
	{
		$this->_validate_user();

		$data['site_name'] = "Psycho Store";
		$data['username'] = 'codinpsycho';
		$data['order_id'] = '5XTGH567';
		$type = $this->input->post('mail_type');
		$data['num_subscribers'] = $this->database->GetNumOfSubscribers();

		if($this->input->post('email') != false)
		{
			$params = mg_create_mail_params($type, $data);
			mg_send_mail($this->input->post('email'), $params);
		}
		
		display('admin_mail', $data);
	}

	function test_mass_mail()
	{
		if($this->input->post('subject') != false)
		{			
			$data['site_name'] = "Psycho Store";
			$data['subscribers'] = $this->database->GetTestEmails();
			$data['num_subscribers'] = count($data['subscribers']);
			$data['subject'] = $this->input->post('subject');
		
			$this->_send_mass_mail($data);
		}
		else
		{
			echo "At least enter somethigng dumbass.";
		}
	}

	function mass_mail()
	{
		if($this->input->post('subject') != false)
		{
			$data['site_name'] = "Psycho Store";
			$data['subscribers'] = $this->database->GetSubscribers(true);
			$data['num_subscribers'] = count($data['subscribers']);

			$data['subject'] = $this->input->post('subject');
		
			$this->_send_mass_mail($data);
		}
		else
		{
			echo "At least enter something dumbass.";
		}
	}

	function _send_mass_mail($data)
	{
		$params = $this->_create_params_for_newsletter($data['subject']);

		if($params)
		{
			$data['params'] = $params;
		}
		else
		{
			die("No such file exists. Please check subject");
		}

		$this->load->view('admin/mass_mail', $data);
	}

	function _create_params_for_newsletter($subject)
	{
		$params = null;

		if(file_exists(APPPATH."views/email/newsletter/$subject-html.php"))
		{
			//Mail params
			$params['subject'] = $subject;
			$params['from'] = 'Psycho Store Updates<email@news.psychostore.in>';
			$params['domain'] = 'news.psychostore.in';
			$params['campaign_id'] = 'psycho_campaign';
			$params['reply_to'] = 'contact@psychostore.in';
			$params['txt'] = $this->load->view("email/newsletter/$subject-txt", $data, TRUE);
			$params['html'] = $this->load->view("email/newsletter/$subject-html", $data, TRUE);	
		}

		return $params;
	}

	function webhooks()
	{
		//Manage mailgun callbacks
		$mailgun_post = $this->input->post();

		switch ($mailgun_post['event'])
		{
			case 'unsubscribed':
				$email = $mailgun_post['recipient'];
				$this->database->Unsubscribe($email);
				mg_unsubscribe($email);
				break;
			
			default:
				# code...
				break;
		}
	}

	function feedback()
	{
		$this->_validate_user();
		//Get all feedbacks from the user, since a certain time
		$feedbacks = $this->database->GetFeedback(false);
		$data['feedbacks_table'] = $this->_generate_feedback_table($feedbacks);
		$data['num_feedbacks'] = count($feedbacks);

		display('admin_feedback', $data);

	}

	function publish_state($id, $value)
	{
		$this->_validate_user();
		$this->database->SetPublishState($id,$value);
		redirect('admin/feedback');
	}

	function request_pickup()
	{
		$this->_validate_user();
		$packaged_shipments = $this->session->flashdata('packaged_shipments');
		
		if(is_null($packaged_shipments))
		{
			redirect('admin/shipments');
		}
		
		$pickup_requested = $this->_request_pickup($packaged_shipments);

		if($pickup_requested)
		{
			foreach ($packaged_shipments as $key => $shipment)
			{
				//Update order status to 'requested'
				$txn_id[] = $shipment['txn_id'];
				$dead_waybills[] =$shipment['waybill'];
			}

			$this->database->UpdateOrderStatus($txn_id, OrderState::Requested);

			//Remove dead waybills once they are manifested
			$this->database->DeleteWaybill($dead_waybills);
		}		

		redirect('admin/shipments');
	}

	function _request_pickup($shipments)
	{
		//Run delhivery script
		$result = request_delhivery_pickup($shipments);
		
		if($result['success'] == false)
		{
			print_r($result);
			die('Pick up request failed');
		}

		return $result['success'];
	}

	function shipments()
	{
		always_refresh();

		$this->_validate_user();

		//Get all packaged and requested orders
		$packaged_shipments = $this->database->GetOrdersByState(OrderState::Packaging);
		$packaged_shipments = $this->_add_address_and_user_to_orders($packaged_shipments);		
		
		$requested_shipments = $this->database->GetOrdersByState(OrderState::Requested);
		$requested_shipments = $this->_add_address_and_user_to_orders($requested_shipments);

		$this->session->set_flashdata('packaged_shipments', $packaged_shipments);		

		$final_shipments = array_merge($packaged_shipments, $requested_shipments);

		$data['pickup_btn_state'] = count($packaged_shipments) > 0 ? '' : 'disabled';
		$data['pickup_requested'] = count($requested_shipments) > 0 ? true : false;
		$data['num_pcikup_shipments'] = count($requested_shipments);
		$data['date'] = date('d-m-y');
		$data['num_pkg_shipments'] = count($packaged_shipments);
		$data['orders_table'] = $this->_generate_orders_table($final_shipments);
		
		display('admin_shipments', $data);
	}

	function update_order($id, $status)
	{
		$this->_validate_user();

		switch ($status)
		{
			case OrderState::Packaging:		
				$this->_package($id);
				break;
			
			case OrderState::Pending:
				$this->_pending($id);
				break;

			case OrderState::Shipped:
				$this->_shipped($id);
				break;

			case OrderState::Returned:
				$this->_returned($id);
				break;				
		}

		redirect('admin/orders');
	}

	function _package($txn_id)
	{
		if(is_array($txn_id) == false)
		{
			$txn_id = array($txn_id);
		}
		
		foreach ($txn_id as $key => $id)
		{			
			$this->database->UpdateOrderStatus($id, OrderState::Packaging);
			$wb = $this->database->GetWaybills();	//Returns an array
			$this->database->AssignWaybill($id, $wb[0]);
		}
	}

	function _pending($txn_id)
	{
		if(is_array($txn_id) == false)
		{
			$txn_id = array($txn_id);
		}

		foreach ($txn_id as $key => $id)
		{
			$order = $this->database->GetOrderById($id);
			$this->database->SetWaybillState($order['waybill'], 'alive');
			$this->database->UpdateOrderStatus($id, OrderState::Pending);
			$this->database->RemoveWaybillFromOrder($id);
		}
	}

	function _shipped($txn_id)
	{
		if(is_array($txn_id) == false)
		{
			$txn_id = array($txn_id);
		}

		foreach ($txn_id as $key => $id)
		{
			$order[] = $this->database->GetOrderById($id);
			$this->_add_address_and_user_to_orders($order);
			$order = $order[0];
			//Mark as shipped
			$this->database->UpdateOrderStatus($id, OrderState::Shipped);
			
			//Collect waybills
			$dead_waybills[] = $order['waybill'];

			//Mail User
			$data['order_id'] = $order['txn_id'];
			$data['username'] = $order['user']['username'];
			$data['waybill'] = $order['waybill'];
			$data['tracking_address'] = $this->config->item('delhivery_url')."/p/{$order['waybill']}";
			$data['site_name'] = $this->config->item('website_name', 'tank_auth');
			$params = mg_create_mail_params('shipped', $data);
			mg_send_mail($order['user']['email'], $params);
		}		

		redirect('admin/shipments');
	}

	function _returned($txn_id)
	{
		if(is_array($txn_id) == false)
		{
			$txn_id = array($txn_id);
		}
		
		foreach ($txn_id as $key => $id)
		{			
			$this->database->UpdateOrderStatus($id, OrderState::Returned);
			$this->database->RemoveWaybillFromOrder($id);
		}
	}

	function _add_address_and_user_to_orders(&$orders)
	{
		//Get user details and address in the array
		foreach ($orders as $key => $value)
		{
			$user = $this->database->GetUserById($value['user_id']);				
			$orders[$key]['user'] = $user;
			$orders[$key]['address'] = $this->database->GetAddressById($value['address_id']);
		}

		return $orders;
	}
	
	function orders($order_id = null)
	{
		$this->_validate_user();
		$orders = array();
		
		if($order_id)
		{
			$ord = $this->database->GetOrderById($order_id);
			if($ord)
			{
				$orders = array($ord);
			}			
		}
		else
		{
			//If no order_id is given show pending/returned orders
			$orders = $this->database->GetPendingReturnedOrders();
		}
		
		
		//As $orders is an array
		if(count($orders))
		{
			$this->_add_address_and_user_to_orders($orders);
		}

		$data['orders'] = $orders;
		$data['num_shipped_orders'] = $this->database->NumShippedOrders();
		$data['num_orders']	= count($orders);
		$data['orders_table'] = $this->_generate_orders_table($orders);

		display('admin_orders', $data);

	}

	function products($product_id = null)
	{
		$this->_validate_user();
		$products = null;
		$data['supported_games'] = $this->database->GetAllSuportedGames();	

		if($product_id)
		{
			$products[] = $this->database->GetProductById($product_id);
		}
		else
		{
			$product_type = $this->input->post('type') != false ? $this->input->post('type') : 'all' ;
			$game = $this->input->post('game') != false ? $this->input->post('game') : 'all' ;
			$sort = $this->input->post('sort') != false ? $this->input->post('sort') : 'latest';
			
			$products = $this->database->GetProducts($product_type, $sort, $game);
		}
		
		if( count($products) && ($products[0] != null) )
		{
			$data['products'] = $products;			
			$data['num_prods'] = count($products);
			$data['products_table'] = $this->_generate_products_table($products);
			display('admin_products', $data);
		}
		else
		{
			display('404', null);
		}
	}

	function add_product()
	{
		$this->_set_product_form_rules();

		if($this->form_validation->run())
		{
			$product = $this->_get_product_form_post($this->input->post());

			$this->database->AddProduct($product);
			redirect('admin/products');
		}
		else
		{
			$data = $this->_fill_data_var_for_view(null);
			$data['action'] = site_url('admin/add_product');
			display('admin_product_add_edit', $data);
		}
	}

	function edit_product($product_id)
	{
		$product = $this->database->GetProductById($product_id);

		if(count($product))
		{
			$this->_set_product_form_rules();

			if($this->form_validation->run())
			{
				$product = $this->_get_product_form_post($this->input->post());
				$product['product_id'] = $product_id;

				$this->database->ModifyProduct($product);
				redirect('admin/products');
			}
			else
			{
				$data = $this->_fill_data_var_for_view($product);
				$data['action'] = site_url('admin/edit_product/'.$product_id);
				display('admin_product_add_edit', $data);
			}
		}
		else
		{
			display('404', null);
		}
		
	}

	function _get_product_form_post($input)
	{
		//All data ok, add this product to database
		$product['product_id'] = $input['id'];
		$product['product_type'] =$input['type'];
		$product['product_game'] = $input['game_name'];
		$product['product_name'] = $input['product_name'];
		$product['product_url'] = strtolower($input['url']);
		$product['product_intro'] = $input['intro'];
		$product['product_desc'] = $input['desc'];
		$product['product_image_path'] = $input['image_path'];
		$product['product_price'] = $input['price'];
		$product['product_count_small'] = $input['s_qty'];
		$product['product_count_medium'] = $input['m_qty'];
		$product['product_count_large'] = $input['l_qty'];
		$product['product_count_xl'] = $input['xl_qty'];

		return $product;
	}

	function _set_product_form_rules()
	{
		$this->form_validation->set_rules('type', 'Product type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('game_name', 'Game Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('product_name', 'Product Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('url', 'URL Keywords', 'trim|required|xss_clean');
		$this->form_validation->set_rules('intro', 'Product Intro', 'trim|required|xss_clean');
		$this->form_validation->set_rules('desc', 'Product Desc', 'trim|required|xss_clean');
		$this->form_validation->set_rules('image_path', 'Image Path', 'trim|required|xss_clean');
		$this->form_validation->set_rules('price', 'Product Price', 'is_numeric|trim|required|xss_clean');
		$this->form_validation->set_rules('s_qty', 'Small Qty', 'is_numeric|trim|required|xss_clean');
		$this->form_validation->set_rules('m_qty', 'Medium Qty', 'is_numeric|trim|required|xss_clean');
		$this->form_validation->set_rules('l_qty', 'Large Qty', 'is_numeric|trim|required|xss_clean');
		$this->form_validation->set_rules('xl_qty', 'XL Qty', 'is_numeric|trim|required|xss_clean');
	}

	function _fill_data_var_for_view($product)
	{		
		$data['type'] = is_null($product) ? '' : $product['product_type'];
		$data['game'] = is_null($product) ? '' : $product['product_game'];
		$data['name'] = is_null($product) ? '' : $product['product_name'];
		$data['product_url'] = is_null($product) ? '' : $product['product_url'];
		$data['intro'] = is_null($product) ? '' : $product['product_intro'];
		$data['desc'] = is_null($product) ? '' : $product['product_desc'];		
		$data['image_path'] = is_null($product) ? '' : $product['product_image_path'];
		$data['price'] = is_null($product) ? '' : $product['product_price'];
		$data['s_qty'] = is_null($product) ? '' : $product['product_count_small'];
		$data['m_qty'] = is_null($product) ? '' : $product['product_count_medium'];
		$data['l_qty'] = is_null($product) ? '' : $product['product_count_large'];
		$data['xl_qty'] = is_null($product) ? '' : $product['product_count_xl'];

		return $data;
	}

	//code to be shifted to view for more flexibility
	function _generate_orders_table($orders)
	{		
		$this->load->library('table');
		$this->table->set_heading('#','Txn_id','Date','Email','Address', 'Mode', 'Amount', 'Status', 'Waybill', 'Process', 'Label');

		$tmpl = array ( 'table_open'  => '<table class="table table-condensed" >' );
		$this->table->set_template($tmpl);

		$num = 1;
		foreach ($orders as $order)
		{
			if(is_null($order))
			{
				continue;
			}

			$txn_id = $order['txn_id'];
			$email = $order['user']['email'];
			$address = format_address($order['address']);
			$date = $order['date_created'];
			$mode = $order['payment_mode'];
			$amount = $order['order_amount'];
			$status = $order['order_state'];
			$waybill = $order['waybill'];
			$order_process_link = null;
			$view_label_link = null;		

			switch ($status)
			{
				case OrderState::Pending:
					$process_link = site_url('admin/update_order/'.$txn_id.'/'.OrderState::Packaging);
					$order_process_link = "<a class ='btn btn-default' href=$process_link> Ship Today </a>";
					break;
				
				case OrderState::Packaging:
					$process_link = site_url('admin/update_order/'.$txn_id.'/'.OrderState::Pending);
					$order_process_link = "<a class ='btn btn-warning' href=$process_link> Don't Ship </a>";
					break;
				
				case OrderState::Returned:
					$process_link = site_url('admin/update_order/'.$txn_id.'/'.OrderState::Packaging);
					$order_process_link = "<a class ='btn btn-default' href=$process_link> Ship Today </a>";
					break;

				case OrderState::Requested:
					$process_link = site_url('admin/update_order/'.$txn_id.'/'.OrderState::Shipped);
					$label_link = site_url('admin/labels/'.$waybill);
					$view_label_link = "<a target='_blank' class ='btn btn-default' href=$label_link> View label</a>";
					$order_process_link = "<a class ='btn btn-danger' href=$process_link> Shipped</a>";
					break;

				case OrderState::Shipped:
					$process_link = site_url('admin/update_order/'.$txn_id.'/'.OrderState::Returned);
					$order_process_link = "<a class ='btn btn-danger' href=$process_link> Returned</a>";
					break;

				default:
					$order_process_link = null;
					$view_label_link = null;
					# code...
					break;
			}	

			$this->table->add_row($num, $txn_id,  $date, $email, $address, $mode, $amount, $status, $waybill, $order_process_link, $view_label_link);

			foreach ($order['order_items'] as $key => $item) 
			{
				$product = $item['product'];
				$product_name = array('data'=> $product['product_name'], 'colspan'=>4, 'align'=>'right');
				$size = array('data' => $item['size'], 'colspan'=>2, 'align'=>'right');
				$count = array('data' => $item['count'], 'colspan'=>2, 'align'=>'right');
				$this->table->add_row( $product_name, $size, $count);
			}
			++$num;	
		}
		
		return $this->table->generate();
	}

	function _generate_products_table($products)
	{
		$this->load->library('table');
		$this->table->set_heading('id', 'type', 'game', 'name', 'url', 'intro', 'description', 'image', 'price', 'small', 'med', 'lrg', 'xl', 'sold');

		$tmpl = array ( 'table_open'  => '<table class="table " >' );
		$this->table->set_template($tmpl);

		foreach ($products as $key => $prod)
		{
			//Edit link
			$product_id = $prod['product_id'];
			$prod_edit_link = site_url('admin/edit_product/'.$product_id);
			$prod_id_cell = "<a href=$prod_edit_link> $product_id </a>";

			//Product Image
			$img_path = site_url($prod['product_image_path']);
			$image_cell = "<a href= $img_path><img class='img-responsive' src = $img_path></img></a>";

			//Product Link			
			$prod_url = product_url($prod);
			$prod_name_cell = anchor($prod_url, $prod['product_name']);

			$this->table->add_row($prod_id_cell, $prod['product_type'], $prod['product_game'], $prod_name_cell, $prod['product_url'], $prod['product_intro'], $prod['product_desc'], $image_cell, $prod['product_price'], $prod['product_count_small'], $prod['product_count_medium'], $prod['product_count_large'], $prod['product_count_xl'], $prod['product_qty_sold']);
		}

		return $this->table->generate();
	}

	function _generate_feedback_table($feedbacks)
	{
		$this->load->library('table');
		$this->table->set_heading('id', 'date', 'name', 'email', 'message', 'Publish');

		$tmpl = array ( 'table_open'  => '<table class="table " >' );
		$this->table->set_template($tmpl);

		foreach ($feedbacks as $key => $fb)
		{
			//Edit link
			$fb_id = $fb['id'];
			$pub_val = !$fb['publish'];

			if($fb['publish'])
			{
				$publish_link = site_url('admin/publish_state/'.$fb_id.'/0');
				$fb_pub_link = "<a class ='btn btn-warning' href=$publish_link> Unpublish </a>";
			}
			else
			{
				$publish_link = site_url('admin/publish_state/'.$fb_id.'/1');
				$fb_pub_link = "<a class ='btn btn-primary' href=$publish_link> Publish </a>";
			}
			

			
			$this->table->add_row($fb_id, $fb['time'], $fb['name'], $fb['email'], $fb['message'], $fb_pub_link);
		}

		return $this->table->generate();
	}

	function _generate_users_table($users)
	{		
		$this->load->library('table');
		$this->table->set_heading('id', 'Date', 'Name', 'Email', 'Points', 'Reward');

		$tmpl = array ( 'table_open'  => '<table class="table " >' );
		$this->table->set_template($tmpl);

		foreach ($users as $key => $user)
		{
			$id = $user['id'];
			$form_url = site_url("admin/users/$id");
			//Reward Link
			$reward_link = "<form class='form-inline' method=\"post\" action= $form_url >
				<div class=\"form-group\">

					<input type='number' name=points  class=\"form-control\" placeholder=\"Points\">
					<button type=\"submit\" class=\"btn btn-primary\">Reward</button>
				</div>
			</form>";			

			$this->table->add_row($user['id'], $user['created'], $user['username'], $user['email'], $user['points'],$reward_link);
		}

		return $this->table->generate();
	}

	function _generate_discount_table($discounts)
	{
		$domains = $discounts['domains'];
		$coupons = $discounts['coupons'];
		$this->load->library('table');
		$this->table->set_heading('id', 'Domain', 'Discount Percentage', 'Update', 'Delete', 'Expiry Date');

		$tmpl = array ( 'table_open'  => '<table class="table " >' );
		$this->table->set_template($tmpl);
		$i = 1;
		foreach ($domains as $key => $domain)
		{
			$id = $i;
			$discount_percentage = $domain['how_much'];
			$domain_name = $domain['domain'];
			$update_url = site_url("admin/update_discount_domain/$domain_name");

			$discount_link = "<form class='form-inline' method=\"post\" action=$update_url >
				<div class=\"form-group\">

					<input type='number' name=discount_percentage  class=\"form-control\" placeholder=\"Discount %\">
					<button type=\"submit\" class=\"btn btn-primary\">Update</button>
				</div>
			</form>";

			$remove_url = site_url("admin/remove_discount_domain/$domain_name/domain");
			$remove_link = "<a class=\"btn btn-danger\" href=$remove_url>Delete</a>";

			$this->table->add_row($id, $domain_name, $discount_percentage, $discount_link, $remove_link);
			$i++;
		}

		foreach ($coupons as $key => $coupon)
		{
			$id = $i;
			$discount_percentage = $coupon['how_much'];
			$coupon_name = $coupon['coupon'];
			$remove_url = site_url("admin/remove_discount/$coupon_name/coupon");
			$remove_link = "<a class=\"btn btn-danger\" href=$remove_url>Delete</a>";
			$expiry_date = $coupon['expiry'];

			$this->table->add_row($id,$coupon_name, $discount_percentage, null, $remove_link, $expiry_date);
			$i++;
		}

		return $this->table->generate();
	}

	function search()
	{
		$search_option = $this->input->post('search_option');
		$search_query = trim($this->input->post('search_query'));

		switch ($search_option)
		{
			case 'orders':
				redirect("admin/orders/$search_query");
				break;

			case 'products':
				redirect("admin/products/$search_query");
				break;
			
			default:
				# code...
				break;
		}
	}
}

?>

