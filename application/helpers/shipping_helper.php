<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('request_delhivery_pickup'))
{
	function request_delhivery_pickup($processing_shipments)
	{
		$ci = &get_instance();
		$ci->config->load('shipping_settings');
		$api_url = $ci->config->item('delhivery_url');
		$token = $ci->config->item('delhivery_token');
		$warehouse = $ci->config->item('delhivery_warehouse');

		$url = $api_url."/cmu/push/json/?token=".$token;		
		
		$params = array(); // this will contain request meta and the package feed
		$package_data = array(); // package data feed
		$shipments = array();
		$pickup_location = array();

		/////////////start: building the package feed/////////////////////
		foreach ($processing_shipments as $key => $value)
		{			
			$ship['waybill'] = $value['waybill']; // waybill number			
			$ship['order'] = $value['txn_id']; // client order number
			$ship['products_desc'] = 'Merchandise for Gamers/Geeks';
			$ship['order_date'] = $value['date_created']; // ISO Format
			$ship['payment_mode'] = $value['payment_mode'];
			$ship['total_amount'] = $value['order_amount']; // in INR

			if($value['payment_mode'] == 'cod')
			{
				$ship['cod_amount'] = $value['order_amount']; // amount to be collected, required for COD
			}

			//Assign Address
			$address = $value['address'];
			$ship['name'] = $address['first_name'].' '.$address['last_name']; // consignee name
			$ship['add'] = $address['address_1'].','.$address['address_2']; // consignee address
			$ship['city'] = $address['city'];
			$ship['state'] = $address['state'];
			$ship['country'] = $address['country'];
			$ship['phone'] = $address['phone_number'];
			$ship['pin'] = $address['pincode'];
			$ship['quantity'] = 1; // quanitity of quantity

			$shipments[] = $ship;
		}

		// pickup location information //
		$pickup_location = $ci->config->item('pickup_location');

		$package_data['shipments'] = $shipments;
		$package_data['pickup_location'] = $pickup_location;
		$params['format'] = 'json';
		$params['data'] =json_encode($package_data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($params));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		$error = curl_error($ch);
		curl_close($ch);

		//print_r($result);
		echo "--------------- result ------------";
		var_dump($error);
		echo "--------------- Info ------------";
		var_dump($info);
 	}
}


?>