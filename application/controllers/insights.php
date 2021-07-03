<?php 

class Insights extends CI_Controller
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
		$this->load->model('database');
	}

	function index()
	{
		$is_admin = $this->_is_user_admin();

		$month = !empty($this->input->post('month')) ? $this->input->post('month') : date("M"); // Coded on 19.04.2021
		
		if($month === false)
		{
			$month = date("M");
		}

		$data['heading'] = "Insights";
		
		$all_orders = $this->database->GetAllOrders();
		$gross = $this->_get_gross_info($all_orders);

		//get latest order
		$latest_order_index = count($all_orders) - 1;
		$latest_order = array($all_orders[$latest_order_index]);
		
		_add_address_and_user_to_orders($latest_order);

		//Get this months orders data
		$month_info = $this->_getOrdersDataForMonth($month);

		$data['gross'] = $gross;
		$data['month'] = $month;
		$data['total_products'] = $month_info['total_products'];
		$data['sales_data'] = $month_info['orders'];
		$data['num_orders'] = $month_info['num_orders'];
		$data['dates'] = $month_info['dates'];
		$data['revenue_data'] = $month_info['revenue'];
		$data['total_revenue'] = $month_info['total_revenue'];
		$data['cod_orders'] = $this->_getNumCodOrders($all_orders);
		$data['online_orders'] = $this->_getNumPrePaidOrders($all_orders);		
		$data['is_admin'] = $is_admin;
		$data['latest_order'] = $latest_order;
		
		//Get Statewise Orders data
		$state_info = $this->_getStateWiseOrderData();

		$data['states'] = $state_info['states'];
		$data['states_sales'] = $state_info['states_sales'];

		//Get Game Sepcific Sales Data
		$games_data = $this->database->GetDataForGameSalesChart();
		foreach ($games_data as $key => $value)
		{
			$data['game_sales_data'][] = (int)$value['product_qty_sold'];
		}

		$all_games = $this->database->GetAllSuportedGames();
		foreach ($all_games as $key => $value)
		{
			$data['all_games'][] = $value['product_game'];
		}

		$data['meta_id'] = 5;


		// echo '<pre>'; print_r($data['all_games']); exit();
		display('insights', $data);
	}

	function _get_gross_info($orders)
	{
		//total orders, products, revenue
		$gross['total_orders'] = count($orders);
		$gross['total_order_items'] = $this->database->GetNumOrderItems();
		$revenue = 0;
		foreach ($orders as $key => $order)
		{
			$revenue += $order['order_amount'];
		}

		$gross['revenue'] = $revenue;

		return $gross;
	}

	function _is_user_admin()
	{
		$current_user = $this->database->GetUserById($this->tank_auth->get_user_id());		
		$admin = false;
		$admin_emails = $this->config->item('admin_email');
		
		foreach ($admin_emails as $key => $email)
		{
			if($current_user)
			{
				if($current_user['email'] == $email )
				{
					$admin = true;
				}
			}			
		}

		return $admin;
	}

	function _getNumCodOrders($orders)
	{
		$count = 0;
		foreach ($orders as $key => $value)
		{
			if($value['payment_mode'] == "cod")
			{
				$count++;
			}
		}

		return $count;
	}


	function _getTotalOrderValuesUsingMode($orders, $mode = 'cod')
	{
		$total = 0;
		foreach ($orders as $key => $value)
		{
			if($value['payment_mode'] == $mode)
			{
				$total += $value['order_amount'];
			}
		}

		return $total;
	}


	function _getNumPrePaidOrders($orders)
	{
		$count = 0;
		foreach ($orders as $key => $value)
		{
			if($value['payment_mode'] == "pre-paid")
			{
				$count++;
			}
		}

		return $count;	
	}

	function _getNumOrdersForDate($date, $orders)
	{
		$start_date = $date. " 00:00:00";
		$end_date = $date. " 23:59:59";
		$sales = 0;
		if($orders != null)
		{
			foreach ($orders as $key => $value)
			{			
				if(strtotime($value['date_created']) >= strtotime($start_date) && strtotime($value['date_created']) <= strtotime($end_date))
				{
					$sales++;
				}
			}
		}

		return $sales;
	}

	function _getRevenueForDate($date, $orders)
	{
		$start_date = $date. " 00:00:00";
		$end_date = $date. " 23:59:59";
		$revenue = 0;
		if($orders != null)
		{
			foreach ($orders as $key => $value)
			{			
				if(strtotime($value['date_created']) >= strtotime($start_date) && strtotime($value['date_created']) <= strtotime($end_date))
				{
					$revenue += $value['order_amount'];
				}
			}
		}

		return $revenue;
	}

	//Expects month as date("M");
	function _getOrdersDataForMonth($month)
	{
		$month_orders = null;
		$month_revenue = null;
		$month_dates = null;
		$year = date("Y");

		$mon_to_num = array('Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04', 'May' => '05', 'Jun' => '06', 'Jul' => '07', 'Aug' => '08', 'Sep' => '09', 'Oct' => '10', 'Nov' =>'11', 'Dec' => '12' );
		$mon = $mon_to_num[$month];
		$start_date = $year."-$mon-01";
		$end_date = $year."-$mon-31";
		$orders = $this->database->GetOrdersForDate($start_date, $end_date);

		for($i = 1; $i<=31; $i++)
		{
			$date = $year."-$mon-$i";
			$month_orders[] = $this->_getNumOrdersForDate($date, $orders);
			$month_revenue[] = $this->_getRevenueForDate($date, $orders);
			$month_dates[] = $i;
		}

		$total_products = 0;
		if($orders)
		{
			foreach ($orders as $key => $order)
			{
				$order_items = $order['order_items'];
				foreach ($order_items as $key => $item)
				{
					$total_products += $item['count'];
				}				
			}
		}

		$month_info['total_products'] = $total_products;
		$month_info['orders'] = $month_orders;
		$month_info['num_orders'] = array_sum($month_orders);
		$month_info['dates'] = $month_dates;
		$month_info['revenue'] = $month_revenue;
		$month_info['total_revenue'] = array_sum($month_revenue);
		
		return $month_info;
	}

	function _getStateWiseOrderData()
	{
		$states_data = $this->database->GetDataForStatesChart();		
		$states = array();
		$states_sales = array();
		foreach ($states_data as $key => $value)
		{
			$states[] = $value['state'];
			$states_sales[] = $value["Count('state')"];
		}

		$state_info['states'] = $states;
		$state_info['states_sales'] = $states_sales;
		
		return $state_info;		
	}



	// developed on 06.05.2021
	function dashboard()
	{
		_validate_user();
		// $data['total_users'] = $this->database->GetAllUsers();
		// $data['total_converted_users'] = $this->database->_getConvertedUsers();

		$data['start_date'] = date('Y-m-d', strtotime("-1 months"));
		$data['end_date'] = date('Y-m-d');

		if(!empty($this->input->post('submit'))) {

			$data['start_date'] = date('Y-m-d', strtotime($this->input->post('start_date')));
			$data['end_date'] = date('Y-m-d', strtotime($this->input->post('end_date')));

		}

		// users
		$results = $this->database->_getUserUsingDateRange($data['start_date'], $data['end_date']);
		foreach ($results as $key => $value) {
			$data['joining_date'][] = date('d', strtotime($value['joining_date']));
			$data['users'][] = $value['count'];

			if(!isset($data['total_users'])) {
				$data['total_users'] = 0;
			}
			$data['total_users'] += $value['count'];
		}


		$results = $this->database->_getUserIDsUsingDateRange($data['start_date'], $data['end_date']);
		foreach ($results as $key => $value) {
			$selected_ids[] = $value['id'];
		}
		$data['user_with_one_order'] = $this->database->_getUserWithOrders(0, 2, $selected_ids);
		$data['user_with_two_order'] = $this->database->_getUserWithOrders(1, 3, $selected_ids);
		$data['user_with_three_order'] = $this->database->_getUserWithOrders(3, 100, $selected_ids);
		$data['returning_users'] = $this->database->_getReturningUsersUsingDateRange($data['start_date'], $data['end_date']);

		$results = $this->database->_getConvertedUserUsingDateRange($data['start_date'], $data['end_date']);
		foreach ($results as $key => $value) {
			$data['converted_users'][] = $value['count'];
			if(!isset($data['total_converted_users'])) {
				$data['total_converted_users'] = 0;
			}
			$data['total_converted_users'] += $value['count'];
		}




		// product type sales
		$products = $this->database->_getProductSalesUsingDateRange($data['start_date'], $data['end_date']);
		$product_types = array();
		if(count($products)) {
			
			foreach ($products as $key => $element) {
				if(!isset($product_types[$element['game']]['total'])) {
					$product_types[$element['game']]['total'] = 0;
				}
				$product_types[$element['game']]['total'] += $element['count'];
				$product_types[$element['game']]['color'] = $this->rand_color();
			}

			foreach ($product_types as $key => $value) {
				$data['product_types'][] = $key;
				$data['product_types_total'][] = $value['total'];
				$data['product_types_color'][] = $value['color'];
			}

		}

		

		// checkout orders
		$data['date_ranges'] = $this->_get_date_range($data['start_date'], $data['end_date']);
		foreach ($data['date_ranges'] as $key => $value) {
			$data['checkouts'][] = $this->database->_getCheckOutOrdersUsingDateRange($value)['count'];
			$data['checkout_dates'][] = date('d', strtotime($value));
		}

		
		
		// design sales
		$results = $this->database->_getDesignSalesUsingDateRange($data['start_date'], $data['end_date']);
		if(count($results) > 0) {
			foreach ($results as $key => $value) {
				$data['design'][] = $value['design'];
				$data['design_sold'][] = $value['count'];
				$data['design_color'][] = "#09f"; // $this->rand_color();
			}
		}




		// Remodeling Insight Page Data Using Date Range on 10.05.2021
		$allOrdersWithInDateRange = $this->database->GetAllOrdersUsingDateRange($data['start_date'], $data['end_date']);

		//Get orders data using date range
		$month_info = $this->_getOrdersDataUsingDateRange($data['start_date'], $data['end_date']);

		$data['sales_data'] = $month_info['orders'];
		$data['sales_dates'] = $month_info['dates'];
		$data['num_orders'] = $month_info['num_orders'];
		$data['total_products'] = $month_info['total_products'];
		$data['revenue_data'] = $month_info['revenue'];
		$data['total_revenue'] = $month_info['total_revenue'];


		$data['cod_orders'] = $this->_getNumCodOrders($allOrdersWithInDateRange);
		$data['online_orders'] = $this->_getNumPrePaidOrders($allOrdersWithInDateRange);
		$data['payment_modes'][] = $data['cod_orders'];
		$data['payment_modes'][] = $data['online_orders'];

		// dev on 02.07.2021
		// total value in INR of pre-paid and COD orders
		$data['cod_values'] = $this->_getTotalOrderValuesUsingMode($allOrdersWithInDateRange, 'cod');
		$data['prepaid_values'] = $this->_getTotalOrderValuesUsingMode($allOrdersWithInDateRange, 'pre-paid');
		// dev on 02.07.2021

		


		// echo '<pre>'; 
		// print_r($data['payment_values']); 
		// print_r($data['payment_modes']); 
		// exit();

		//Get Statewise Orders data
		$state_infos = $this->database->_getStateWiseOrderUsingDateRange($data['start_date'], $data['end_date']);
		$state_wise_date = array();
		foreach ($state_infos as $key => $element) {
			if(!isset($state_wise_date[$element['state']]['total'])) {
				$state_wise_date[$element['state']]['total'] = 0;
			}
			$state_wise_date[$element['state']]['total'] += $element['count'];
		}

		foreach ($state_wise_date as $key => $value) {
			$data['states'][] = $key;
			$data['states_sales'][] = $value['total'];
			$data['states_colors'][] = "#09f"; //$this->rand_color();
		}

		// Remodeling Insight Page Data Using Date Range on 10.05.2021
		display('admin_dashboard', $data);
	}


	function rand_color() 
	{
		return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
	}



	function _getOrdersDataUsingDateRange($start_date, $end_date)
	{
		$month_orders = null;
		$month_revenue = null;
		$month_dates = null;

		$orders = $this->database->GetOrdersForDate($start_date, $end_date);
		$date_ranges = $this->_get_date_range($start_date, $end_date); 

		foreach ($date_ranges as $key => $value) 
		{
			$date = $value;
			$month_orders[] = $this->_getNumOrdersForDate($date, $orders);
			$month_revenue[] = $this->_getRevenueForDate($date, $orders);
			$month_dates[] = date('d', strtotime($value));
		}


		$total_products = 0;
		if($orders)
		{
			foreach ($orders as $key => $order)
			{
				$order_items = $order['order_items'];
				foreach ($order_items as $key => $item)
				{
					$total_products += $item['count'];
				}				
			}
		}

		$month_info['total_products'] = $total_products;
		$month_info['orders'] = $month_orders;
		$month_info['num_orders'] = array_sum($month_orders);
		$month_info['dates'] = $month_dates;
		$month_info['revenue'] = $month_revenue;
		$month_info['total_revenue'] = array_sum($month_revenue);

		return $month_info;
	}



	function _get_date_range($first, $last, $step = '+1 day', $output_format = 'Y-m-d' ) 
	{

		$dates = array();
		$current = strtotime($first);
		$last = strtotime($last);

		while( $current <= $last ) {

			$dates[] = date($output_format, $current);
			$current = strtotime($step, $current);
		}

		return $dates;
	} 


}

?>