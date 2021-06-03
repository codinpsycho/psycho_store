<?php
/**
* 
*/
class Database extends CI_Model
{
	
	function __construct()
	{
		$this->load->database();
	}

	//For single product
	function GetProductById($id)
	{
		$this->db->where('product_id', $id);
		$query = $this->db->get('products');
		$product = $query->row_array();

		if($query->num_rows() > 0)
		{
			$this->GetProductDetails($product);
		}

		return $product;
	}

	function GetProductByURL($url)
	{
		$this->db->where('product_url', $url);
		$query = $this->db->get('products');
		return $query->row_array();
	}

	function GetMaxProductID()
	{
		$result = $this->db->get('products');
		$row = $result->last_row('array');		
		return $row['product_id'];
	}

	function GetProductCount()
	{
		return $this->db->count_all('products');
	}

	function GetAllSuportedGames()
	{
		$this->db->distinct();
		$this->db->select('product_game');
		$this->db->order_by('product_game');
		$query = $this->db->get('products');
		return $query->result_array();
	}

	function GetProducts($type, $sort, $game_name = 'all', $featured = false, $search_filter = false)
	{	
		if($type != 'all')
			$this->db->where('product_type', $type);
		if($game_name != 'all')
			$this->db->like('product_game', $game_name);

		if($sort == 'latest')
			$this->db->order_by('product_id', 'desc');
		else if($sort =='popular')				
			$this->db->order_by('product_qty_sold', 'desc');	//Sort by selling amount

		if($featured)	//Gets only featured products
		$this->db->where('featured', 1);

		if($search_filter)
		{
			$this->db->like('product_name', $search_filter);
			$this->db->or_like('product_game', $search_filter);
			$this->db->or_like('product_type', $search_filter);
			$this->db->or_like('product_url', $search_filter);
		}

		//Dont get hidden products
		$this->db->where('product_state !=', 'hidden');

		$query = $this->db->get('products');
		
		$products = $query->result_array();

		foreach ($query->result_array() as $key => $prod)
		{
			$products[$key]['product_details'] = $this->GetProductDetails($prod);
		}

		return $products;
	}

	function GetProductDetails(&$product)
	{
		$product_id = $product['product_id'];
		$prod_type = $product['product_type'];
		$prod_details = null;

		//Add Product Details based on what type it is
		switch ($prod_type)
		{			
			case 'tshirt':
			$prod_details = $this->GetTshirtDetails($product_id);
			break;

			case 'mobilecover':
			$prod_details = $this->GetMobileCoverDetails($product_id);
			break;
			
			default:
				# code...
			break;
		}

		$product['product_details'] = $prod_details;

		return $prod_details;
	}

	function GetSupportedMobileModels()
	{		
		$query = $this->db->get('mobile_models');
		return $query->result_array();
	}

	function GetMobileCoverDetails($product_id)
	{
		$this->db->where('product_id', $product_id);
		$query = $this->db->get('mobile_cover_details');
		return $query->row_array();
	}	

	function GetTshirtDetails($product_id)
	{
		$this->db->where('product_id', $product_id);
		$query = $this->db->get('tshirt_details');
		return $query->row_array();
	}

	function GetSupportedProductsForDesign($design_id, $select = false)
	{
		$this->db->where('design_id', $design_id);
		$query = $this->db->get('products');
		return $query->result_array();
	}

function GetRandomProducts ($count, $type, $game_name, $exceptions/*pass an array with exception values*/)
{	
	$prods = $this->GetProducts($type,'latest',$game_name);
	foreach ($prods as $key => $value)
	{
		if($exceptions)
		{
			foreach ($exceptions as $ex_key => $ex_value)
			{
				if( (string)$value['product_id'] === (string)$ex_value['product_id'] )
					unset($prods[$key]);
			}				
		}
	}		
	$max_prods = count($prods);

	if($count > $max_prods )
		$count = $max_prods;

	$random_ids = array_rand ($prods, $count);
	$random_prods = array();

	foreach ($random_ids as $key => $value) 
		$random_prods[] = $prods[$value];

	return $random_prods;
}

function AddProduct($product)
{		
	$product_with_details = $product['product_details'];
	$galleries = $product['galleries'];

		unset($product['product_details']);			//Unset it here in the last step
		unset($product['galleries']);			//Unset it here in the last step
		$this->db->insert('products',$product);
		$product_with_details['product_id'] = $this->GetProductCount() + 1;

		//To get the latest product_id
		$sql = "Select product_id From products
		ORDER BY product_id desc
		LIMIT 1";
		$query = $this->db->query($sql);
		$last_id = $query->row_array();
		$product_with_details['product_id'] = $last_id['product_id'];
		$this->AddProductDetails($product_with_details,$product['product_type'] );



		if(count($galleries) > 0) {

			foreach ($galleries as $key => $value) {

				if(!empty($value)) 
				{
					$data = array(
						'product_id'=> $last_id['product_id'],
						'imgurl'=> trim($value)
					);

					$this->db->insert('galleries',$data);
				}

			}
		}
		



	}

	function AddProductDetails($product, $type)
	{
		switch ($type)
		{
			case 'tshirt':
			$this->AddTshirtDetails($product);
			break;
			// case 'mugs':
			// 	$this->AddMugsDetails($product['product_details']);
			// 	break;
			// case 'mobilecover':
			// 	$this->AddMobileCoverDetails($product['product_details']);
			break;

			default:
				# code...
			break;
		}
	}

	function AddMobileCoverDetails($prod_details)
	{
		$this->db->insert('mobilecover_details',$product_details);	
	}	

	function AddMugsDetails($prod_details)
	{
		$this->db->insert('mugs_details',$product_details);	
	}

	function AddTshirtDetails($product_details)
	{
		$this->db->insert('tshirt_details',$product_details);
	}

	function ModifyProductDetails($product)
	{
		switch ($product['product_type'])
		{
			case 'tshirt':
			$this->ModifyTshirtDetails($product['product_details']);
			break;
			
			default:
				# code...
			break;
		}
	}

	function ModifyTshirtDetails($product_details)
	{
		$this->db->where('product_id', $product_details['product_id']);
		$this->db->update('tshirt_details', $product_details);
	}

	function ModifyProduct($product)
	{
		$this->ModifyProductDetails($product);
		
		unset($product['product_details']);		//Do it here in the final step
		unset($product['galleries']);		//Do it here in the final step
		$this->db->where('product_id', $product['product_id']);
		$this->db->update('products', $product);
	}

	function GetUserById($id)
	{
		$this->db->where('id', $id);
		$query = $this->db->get('users');
		return $query->row_array();
	}

	function GetAllUsers()
	{
		$query = $this->db->get('users');
		return $query->result_array();
	}

	function GetAddressById($id)
	{
		$this->db->where('address_id', $id);
		$query = $this->db->get('address');
		return $query->row_array();
	}

	function GetAddressesForUser($userid)
	{
		$this->db->where('user_id', $userid);
		$query = $this->db->get('address');
		return $query->result_array();
	}

	function RegisterAddress($data)
	{
		$this->db->insert('address',$data);

		// dev on 14.05.2021
		$insertId = $this->db->insert_id();
   		return  $insertId;
	}

	function RemoveAddress($id)
	{
		$this->db->where('address_id', $id);
		$this->db->delete('address');
	}

	function AddFeedback($feedback)
	{
		$this->db->insert('feedback', $feedback);
	}

	function RemoveFeedback($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('feedback');
	}

	function GetFeedback($published_only)
	{
		if($published_only)
		{
			$this->db->where('publish', 1);
		}

		$query = $this->db->get('feedback');
		return $query->result_array();
	}

	function SetPublishState($id, $value)
	{
		$this->db->set('publish', $value);
		$this->db->where('id', $id);		
		$this->db->update('feedback');
	}

	//-------------------------- Order Specific Functions -------------------------- 

	function AddOrder($order)
	{
		$this->db->insert('orders',$order);
	}

	//It expects a single order Item
	function AddOrderItem($item)
	{
		$this->db->insert('order_items',$item);
	}

	function GetNumOrders()
	{
		return $this->db->count_all('orders');
	}

	function GetNumOrderItems()
	{
		return $this->db->count_all('order_items');
	}

	//Returns both order and order_items
	//order_items are stored in 'order_items' key;
	function GetOrderById($txn_id)
	{
		$order = null;
		$this->db->where('txn_id', $txn_id);
		$query = $this->db->get('orders');		

		if($query->num_rows() > 0)	
		{
			//Get order_items for this txn_id			
			$this->db->where('txn_id', $txn_id);
			$items_query = $this->db->get('order_items');

			$row = $query->row_array();

			//Get the product id and add actual products in 'order_items' array
			$order_items = $items_query->result_array();
			foreach ($items_query->result_array() as $key => $item)
			{
				$order_items[$key]['product'] = $this->GetProductById($item['product_id']);
			}

			$row['order_items'] = $order_items;

			$order = $row;			
		}	

		return $order;
	}

	function UpdateOrderStatus($txn_id, $state)
	{
		$this->db->set('order_state', $state);
		$this->db->where_in('txn_id', $txn_id);
		$query = $this->db->update('orders');
	}

	function AssignWaybill($txn_id, $waybill)
	{
		$this->db->set('waybill', $waybill);
		$this->db->where('txn_id', $txn_id);
		$this->db->update('orders');
	}

	function RemoveWaybillFromOrder($txn_id)
	{
		$this->db->set('waybill', null);
		$this->db->where('txn_id', $txn_id);
		$this->db->update('orders');
	}

	function GetOrdersByState($state)
	{
		$this->db->select('txn_id');
		$this->db->where('order_state =', $state);
		$this->db->limit(500);
		$query = $this->db->get('orders');
		$orders = array();

		foreach ($query->result_array() as $row)
		{
			$orders[] = $this->GetOrderById($row['txn_id']);
		}

		return $orders;
	}

	function GetPendingReturnedOrders()
	{

		$this->db->select('txn_id');
		$this->db->where('order_state =', 'pending');
		$this->db->or_where('order_state =', 'returned');
		$query = $this->db->get('orders');
		$orders = array();

		foreach ($query->result_array() as $row)
		{
			$orders[] = $this->GetOrderById($row['txn_id']);
		}

		return $orders;
	}

	function GetAllOrders()
	{
		$this->db->select('txn_id');	
		$query = $this->db->get('orders');
		$orders = array();
		foreach ($query->result_array() as $row)
		{
			$orders[] = $this->GetOrderById($row['txn_id']);
		}

		return $orders;
	}

	function NumShippedOrders()
	{
		$this->db->where('order_state', 'shipped');
		$this->db->from('orders');
		return $this->db->count_all_results();
	}

	//Format is "y-m-d"
	function GetOrdersForDate($start_date, $end_date)
	{
		$start_date = $start_date." 00:00:00";
		$end_date = $end_date." 23:59:59";		
		
		$this->db->select(array('txn_id', 'date_created'));
		$this->db->having(array('date_created >= ' => $start_date, 'date_created <= ' => $end_date));
		$query = $this->db->get('orders');		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$orders[] = $this->GetOrderById($row['txn_id']);
			}
			return $orders;
		}

		return null;
	}

	//-------------------------- XXX -------------------------- 

	function GetDataForGameSalesChart()
	{		
		$this->db->select_sum('product_qty_sold');
		$this->db->group_by('product_game');
		$query = $this->db->get('products');
		return $query->result_array();
	}

	function GetDataForStatesChart()
	{
		$sql = "Select *,Count('state') From ( SELECT address.state
		FROM address
		INNER JOIN orders
		ON orders.address_id=address.address_id ) as t
		Group By `state`";

		$query = $this->db->query($sql);		
		return $query->result_array();
	}

	function GetOrdersForUser($userId, $detailed = false)
	{
		$this->db->where('user_id', $userId);
		$query = $this->db->get('orders');
		$final_orders = $query->result_array();

		if( count($final_orders) && $detailed )
		{
			$order_array = $query->result_array();

			foreach ($order_array as $key => $value)
			{
				$orders[] =  $this->GetOrderById($value['txn_id']);
			}

			$final_orders = $orders;
		}

		return $final_orders;
	}

	function RewardUser($user_id, $points)
	{
		$this->db->set('points', $points);
		$this->db->where('id', $user_id);		
		$this->db->update('users');
	}

	function Subscribe($email_id)
	{
		$ret = false;
		//make sure it isnt already present
		$this->db->where('email', $email_id);
		$query = $this->db->get('newsletter');
		if($query->num_rows() == 0)
		{
			$email = array('email' => $email_id);
			$this->db->insert('newsletter',$email);
			$ret = true;
		}

		return $ret;
	}

	function SubscriberUpdated($email)
	{		
		$this->db->set('last_update', date('Y-m-d H:i:s'));
		$this->db->where('email', $email);
		$this->db->update('newsletter');
	}

	function GetSubscribersForUpdate($update_threshold = '60*5')
	{
		//Get only those who havent been updated recently
		$this->db->where('UNIX_TIMESTAMP(last_update) <', time() - $update_threshold);
		$query = $this->db->get('newsletter');
	}
	
	//null means get all subscribers
	function GetSubscribers($num = null)
	{
		if($num)
		{
			$this->db->limit($num);
		}
		$this->db->order_by('register_time', 'desc');
		$query = $this->db->get('newsletter');
		return $query->result_array();
	}

	function GetTestEmails()
	{
		$query = $this->db->get('test_mails');
		return $query->result_array();	
	}

	function Unsubscribe($email)
	{
		$this->db->where('email_id', $email);
		$this->db->delete('email_id');
	}

	function GetNumOfSubscribers()
	{
		return $this->db->count_all('newsletter');
	}

	function SetWaybillState($waybill, $state)
	{
		$this->db->set('state', $state);
		$this->db->where_in('waybill', $waybill);
		$this->db->update('delhivery_waybills');
	}

	function DeleteWaybill($waybill)
	{		
		$this->db->where_in('waybill', $waybill);
		$this->db->delete('delhivery_waybills');
	}

	function NumWaybills()
	{
		return $this->db->count_all('delhivery_waybills');
	}

	function InsertWaybills($waybills)
	{
		if(is_array($waybills))
		{
			$this->db->insert_batch('delhivery_waybills', $waybills);
		}
		else
		{
			$this->db->insert('delhivery_waybills', $waybills);
		}
	}

	function GetWaybills($count = 1)
	{
		$wb = null;
		$this->db->where('state !=', 'dead');
		$this->db->limit($count);
		$query = $this->db->get('delhivery_waybills');
		$result = $query->result_array();

		foreach ($result as $key => $waybill)
		{
			$wb[] = $waybill['waybill'];
		}

		$this->SetWaybillState($wb, 'dead');

		return $wb;
	}

	function NumPincodes()
	{
		return $this->db->count_all('delhivery');
	}
	
	//-------------------------- Checkout Specific Functions -------------------------- 	

	//For now its only Delhivery
	function GetShippingDetails($pincode)
	{
		$this->db->where('pincode', $pincode);
		$query = $this->db->get('delhivery');
		return $query->row_array();
	}

	function GetCheckoutOrder($txn_id = null)
	{
		if($txn_id)
		{
			$this->db->where('txn_id', $txn_id);
		}		

		$query = $this->db->get('checkout_orders');
		return $txn_id ? $query->row_array() : $query->result_array();
	}

	function GetCheckoutOrderItems($txn_id)
	{
		$this->db->where('txn_id', $txn_id);
		$query = $this->db->get('checkout_items');
		return $query->result_array();	
	}	

	function CheckoutDone($txn_id)
	{
		$this->db->where('txn_id', $txn_id);
		$this->db->delete('checkout_orders');

		$this->db->where('txn_id', $txn_id);
		$this->db->delete('checkout_items');
	}

	function SaveTxnIdOnCheckout($txn_id)
	{
		$this->db->set('txn_id', $txn_id);
		$this->db->insert('checkout_orders');
	}

	function SaveAmountOnCheckout($amount, $txn_id)
	{
		$this->db->set('order_amount', $amount);
		$this->db->where('txn_id', $txn_id);
		$this->db->update('checkout_orders');
	}

	//It expects a single cart item
	function SaveCartItemOnCheckout($item)
	{
		$this->db->insert('checkout_items',$item);
	}

	function RemoveCheckoutItemsForTxnId($txn_id)
	{
		$this->db->where('txn_id', $txn_id);
		$this->db->delete('checkout_items');
	}

	function SaveUserIdOnCheckout($user_id, $txn_id)
	{
		$this->db->set('user_id', $user_id);
		$this->db->where('txn_id', $txn_id);
		$this->db->update('checkout_orders');
	}

	function SaveAddressOnCheckout($address_id, $txn_id)
	{
		$this->db->set('address_id', $address_id);
		$this->db->where('txn_id', $txn_id);
		$this->db->update('checkout_orders');
	}

	function LockCheckoutOrder($txn_id)
	{
		$this->db->set('state', 'locked');
		$this->db->where('txn_id', $txn_id);
		$this->db->update('checkout_orders');	
	}
	//-------------------------- XXX -------------------------- 

	//----------------- Discount related functions -----------------
	
	//To see what people are entering
	function SaveCheatCode($cheat_code)
	{
		$code_info['cheat_code'] = $cheat_code;
		$this->db->insert('applied_cheat_codes', $code_info);
	}

	function ConsumeCode($code)
	{
		$this->db->set('use_count', 'use_count + 1', FALSE);
		$this->db->where('coupon', $code);
		$this->db->update('discount_coupons');
	}

	function GetCheatCodes($code = null, $count = null)
	{
		if($count)
		{
			$this->db->limit($count);
		}

		if($code)
		{
			$this->db->where('cheat_code', $code);
		}

		$query = $this->db->get('applied_cheat_codes');
		return $query->result_array();
	}

	function ClearCheatCodes()
	{
		$this->db->truncate('applied_cheat_codes');
	}

	function AddDiscountDomain($domain_info)
	{
		$this->db->insert('discount_domains',$domain_info);
	}

	function RemoveDiscountDomain($domain_name)
	{
		$this->db->where('domain', $domain_name);
		$this->db->delete('discount_domains');
	}

	function GetDiscountDomain($domain_name = null)
	{
		if(is_null($domain_name) == false)
		{
			$this->db->where('domain', $domain_name);
		}

		$query = $this->db->get('discount_domains');

		return is_null($domain_name)  ? $query->result_array() : $query->row_array() ;
	}

	function SetDiscountForDomain($domain, $discount_percentage)
	{
		$this->db->set('how_much', $discount_percentage);
		$this->db->like('domain', $domain);
		$this->db->update('discount_domains');
	}

	function AddDiscountCoupon($coupon)
	{
		$this->db->insert('discount_coupons', $coupon);
	}

	function RemoveDiscountCoupon($coupon)
	{
		$this->db->where('coupon', $coupon);
		$this->db->delete('discount_coupons');
	}

	function GetDiscountCoupon($coupon = null)
	{
		if(is_null($coupon) == false)
		{
			$this->db->where('coupon', $coupon);
		}
		
		$query = $this->db->get('discount_coupons');

		return is_null($coupon) ? $query->result_array() : $query->row_array();
	}

	//-------------------------- XXX --------------------------------------- 

	//-------------------------- Meta Info ---------------------------------

	function GetMetaInfoById($meta_id)
	{
		$this->db->where('metainfo_id', $meta_id);
		$query = $this->db->get('metainfo');
		return $query->row_array();
	}

	function GetMetaInfoByName($name)
	{
		$this->db->like('page_name', $name);
		$query = $this->db->get('metainfo');
		return $query->row_array();
	}

	// added on 21.01.2021
	function _updateOrderTrackingDetail($order, $tracking_link)
	{
		$this->db->set('tracking_link', $tracking_link);
		$this->db->where('order_id', $order['order_id']);
		$this->db->update('orders');
	}
	// added on 21.04.2021

	// added on 22.04.2021
	function _getProductGalleries($productID)
	{
		$this->db->order_by('id', 'RANDOM');	
		$this->db->where('product_id', $productID);
		$query = $this->db->get('galleries');
		return $query->result_array();
	}

	function _delProductGalleries($productID)
	{
		$this->db->where('product_id', $productID);
		$this->db->delete('galleries');
	}
	// added on 22.04.2021

	// dev on 04.05.2021
	function _getProductCategories()
	{
		$query = $this->db->get('categories');
		return $query->result_array();
	}

	function _storeProductCategory($name)
	{
		$data = array(
			'name'=> $name,
		);

		$this->db->insert('categories',$data);
	}

	function _getProductCategory($id)
	{
		$this->db->where('id', $id);
		$query = $this->db->get('categories');
		return $query->row_array();
	}

	function _updateProductCategory($id, $name)
	{

		$this->db->set('name', $name);
		$this->db->where('id', $id);
		$this->db->update('categories');

	}


	function GetCategoryWiseProducts($categoryID, $productID = false, $same_designed_ids = false)
	{	

		if($productID)
			$this->db->where_not_in('product_id', [$productID]);

		if($same_designed_ids)
			$this->db->where_not_in('product_id', $same_designed_ids);


		$this->db->where('category_id', $categoryID);
		//Dont get hidden products
		$this->db->where('product_state !=', 'hidden');		

		$query = $this->db->get('products');
		
		$products = $query->result_array();

		foreach ($query->result_array() as $key => $prod)
		{
			$products[$key]['product_details'] = $this->GetProductDetails($prod);
		}

		return $products;
	}


	function GetSuggestedProducts($type, $sort, $game_name = 'all', $productID = false, $same_designed_ids = false)
	{	
		if($type != 'all')
			$this->db->where('product_type', $type);
		if($game_name != 'all')
			$this->db->like('product_game', $game_name);

		if($sort == 'latest')
			$this->db->order_by('product_id', 'desc');
		else if($sort =='popular')				
			$this->db->order_by('product_qty_sold', 'desc');	//Sort by selling amount

		if($productID)
			$this->db->where_not_in('product_id', [$productID]);

		if($same_designed_ids)
			$this->db->where_not_in('product_id', $same_designed_ids);


		//Dont get hidden products
		$this->db->where('product_state !=', 'hidden');		

		$query = $this->db->get('products');
		
		$products = $query->result_array();

		foreach ($query->result_array() as $key => $prod)
		{
			$products[$key]['product_details'] = $this->GetProductDetails($prod);
		}

		return $products;
	}
	// dev on 04.05.2021

	// Dev on 06.05.2021
	function _getUserUsingDateRange($start_date, $end_date)
	{
		$sql = "SELECT date(`created`) as joining_date, COUNT(id) as count FROM users WHERE DATE_FORMAT(created, '%Y-%m-%d') >= '$start_date' AND DATE_FORMAT(created, '%Y-%m-%d') <= '$end_date' GROUP BY date(`created`)";
		$query = $this->db->query($sql);
		return $query->result_array();
	}


	function _getUserIDsUsingDateRange($start_date, $end_date)
	{
		$sql = "SELECT id FROM users WHERE DATE_FORMAT(created, '%Y-%m-%d') >= '$start_date' AND DATE_FORMAT(created, '%Y-%m-%d') <= '$end_date'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}


	function _getConvertedUsers()
	{
		$sql = "SELECT * FROM `users` WHERE id IN(SELECT user_id FROM `orders` ORDER BY `order_id` ASC)";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function _getUserWithOrders($val1, $val2, $selected_ids = [])
	{
		
		// $sql = "SELECT user_id, COUNT(*) AS cnt FROM orders WHERE user_id IN($selected_ids) GROUP BY user_id HAVING cnt > $val1 AND cnt <$val2";

		$this->db->select('user_id');
		$this->db->select('COUNT(*) AS cnt');

		if(count($selected_ids)) 
		$this->db->where_in('user_id', $selected_ids);

		$this->db->group_by('user_id');
		$this->db->having('cnt > ', $val1);
		$this->db->having('cnt < ', $val2);
		$query = $this->db->get('orders');

		// echo $this->db->last_query();
		return $query->result_array();
	}
	// Dev on 06.05.2021

	// Dev on 07.05.2021
	function _getProductSalesUsingDateRange($start_date, $end_date)
	{
		$sql = "SELECT COUNT(product_id) as count, product_id FROM `order_items` WHERE txn_id IN( SELECT txn_id FROM `orders` WHERE DATE_FORMAT(date_created, '%Y-%m-%d') >= '$start_date' AND DATE_FORMAT(date_created, '%Y-%m-%d') <= '$end_date' ORDER BY `order_id` ASC ) GROUP BY product_id";
		$query = $this->db->query($sql);
		$result = $query->result_array();

		foreach ($result as $key => $value) {
			$result[$key]['game'] = $this->GetProductAttributeById($value['product_id'], 'product_game');
		}

		return $result;
	}


	function GetProductAttributeById($id, $attribute = 'product_game')
	{
		$this->db->select($attribute);
		$this->db->where('product_id', $id);
		$query = $this->db->get('products');
		$product = $query->row();
		return $product->$attribute;
	}


	function _getDesignName($designID)
	{
		$this->db->select('design_name');
		$this->db->where('design_id', $designID);
		$query = $this->db->get('designs');
		return $query->row_array();
	}


	function _getCheckOutOrdersUsingDateRange($date)
	{
		$sql = "SELECT COUNT(txn_id) as count, DATE_FORMAT(date_created, '%Y-%m-%d') as created FROM `checkout_orders` WHERE DATE_FORMAT(date_created, '%Y-%m-%d') = '$date'";
		$query = $this->db->query($sql);
		$result = $query->row_array();
		return $result;
	}


	function _getDesignSalesUsingDateRange($start_date, $end_date)
	{
		$sql = "SELECT count(product_id) as count, product_id FROM `order_items` WHERE txn_id IN(SELECT txn_id FROM `orders` WHERE DATE_FORMAT(date_created, '%Y-%m-%d') >= '$start_date' AND DATE_FORMAT(date_created, '%Y-%m-%d') <= '$end_date' ORDER BY `order_id` ASC) GROUP BY product_id ORDER BY count DESC";

		$query = $this->db->query($sql);
		$result = $query->result_array();

		foreach ($result as $key => $value) {
			$result[$key]['design'] = $this->_getDesignUsingProductID($value['product_id']);
		}

		return $result;
	}

	function _getDesignUsingProductID($productID)
	{
		$product = $this->GetProductById($productID);
		return $product['product_name'];
	}


	function GetAllOrdersUsingDateRange($start_date, $end_date)
	{
		$this->db->select('txn_id');	
		$this->db->where("DATE_FORMAT(date_created, '%Y-%m-%d') >= '$start_date'");
		$this->db->where("DATE_FORMAT(date_created, '%Y-%m-%d') <= '$end_date'");
		$query = $this->db->get('orders');
		$orders = array();
		foreach ($query->result_array() as $row)
		{
			$orders[] = $this->GetOrderById($row['txn_id']);
		}

		return $orders;
	}
	

	function _getStateWiseOrderUsingDateRange($start_date, $end_date)
	{
		$sql = "SELECT COUNT(txn_id) as count, address_id FROM `orders` WHERE DATE_FORMAT(date_created, '%Y-%m-%d') >= '$start_date' AND DATE_FORMAT(date_created, '%Y-%m-%d') <= '$end_date' GROUP BY address_id";
		$query = $this->db->query($sql);
		$result = $query->result_array();

		foreach ($result as $key => $value) {
			$result[$key]['state'] = $this->GetStateUsingAddressById($value['address_id']);
		}

		return $result;
	}


	function GetStateUsingAddressById($id)
	{
		$this->db->select('state');	
		$this->db->where('address_id', $id);
		$query = $this->db->get('address');
		$data = $query->row();
		return $data->state;
	}

	// converted user is one who has placed an order
	function _getConvertedUserUsingDateRange($start_date, $end_date)
	{
		$sql = "SELECT date(`created`) as joining_date, count(id) as count, id FROM users WHERE DATE_FORMAT(created, '%Y-%m-%d') >= '$start_date' AND DATE_FORMAT(created, '%Y-%m-%d') <= '$end_date' AND id IN(SELECT user_id FROM `orders` ORDER BY `order_id` ASC) GROUP BY date(`created`)";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	// a user who places an order within the selected date range but has signed up before the date range.
	// so lets say we select a date range of a week, I have placed an order in that week only but I have registered earlier, can be 2 weeks back, month back or an year back, anything
	function _getReturningUsersUsingDateRange($start_date, $end_date)
	{
		$sql = "SELECT * FROM `users` WHERE DATE_FORMAT(created, '%Y-%m-%d') < '$start_date' AND id IN(SELECT user_id FROM `orders` WHERE DATE_FORMAT(date_created, '%Y-%m-%d') >= '$start_date' AND DATE_FORMAT(date_created, '%Y-%m-%d') <= '$end_date' GROUP BY user_id ORDER BY `order_id` ASC)";
		$query = $this->db->query($sql);
		return $query->result_array();
	}


	function _updateAttributeOnCheckoutOrdersIfPointsApplied($data, $txn_id, $state = 'open')
	{
		$this->db->where('txn_id', $txn_id);
		$this->db->where('state', $state);
		$this->db->update('checkout_orders', $data);
	}


	function _updateCustomerAddress($address_id, $data)
	{
		$this->db->where('address_id', $address_id);
		$this->db->update('address', $data);
	}

	function _getLastAddressAsDefaultUserAddress($user_id)
	{
		$sql = "SELECT * FROM `address` WHERE user_id = $user_id ORDER BY address_id DESC LIMIT 1";
		$query = $this->db->query($sql);
		return $query->row_array();
	}


	function updateUserPoints($user_id, $points)
	{
		$user = $this->GetUserById($user_id);
		if($user['points'] >= $points) 
		{
			$updatedPoints = $user['points'] - $points;

			$this->db->set('points', $updatedPoints);
			$this->db->where('id', $user_id);
			$this->db->update('users');
		}
	}


	function GetUserByEmail($email)
	{
		$this->db->where('email', $email);
		$query = $this->db->get('users');
		return $query->row_array();
	}


	function _getProviderDetailUsingSecretKey($secret_key)
	{
		$this->db->where('secret_key', $secret_key);
		$query = $this->db->get('providers');
		return $query->row_array();
	}



	function _checkIsUserRefCodeValid($ref_code, $user_id = null)
	{
		$this->db->where('ref_code', $ref_code);

		if($user_id)
		$this->db->where("id !=", $user_id);

		$query = $this->db->get('users');
		return $query->row_array();
	}

	// dev on 31.05.2021
	function _getProductGalleryImagesRandomly()
	{
		$this->db->order_by('id', 'RANDOM');
		$query = $this->db->get('galleries');
		return $query->result_array();
	}


	// dev on 01.06.2021
	function _getGamesNameWithCategory()
	{
		$this->db->select('t1.game_name, t2.name as category_name');
		$this->db->from('category_games as t1');

		$this->db->join('categories as t2', 't1.category_id = t2.id', 'LEFT');
		$this->db->order_by('t1.game_name');
		$query = $this->db->get();


		if($query->num_rows() != 0)
		{
			return $query->result_array();
		}
		else
		{
			return [];
		}
	}


	function _getCategoryNameUsingGameName($game_name = null)
	{
		$this->db->select('t1.game_name, t2.name as category_name');
		$this->db->from('category_games as t1');

		if(!empty($game_name))
		$this->db->where('t1.game_name', $game_name);

		$this->db->join('categories as t2', 't1.category_id = t2.id', 'LEFT');
		$this->db->order_by('t1.game_name');
		$query = $this->db->get();


		if($query->num_rows() != 0)
		{
			return $query->row_array();
		}
		else
		{
			return false;
		}
	}




}
?>