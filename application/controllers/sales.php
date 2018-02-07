<?php 

require APPPATH.'third_party/phpspreadsheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class sales extends CI_controller
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
		//This month's sales data
		//$this->orders();
		_validate_user();
		$this->process_data();		
	}

	function process_data()
	{
		
		$start = $this->input->post('start_date');
		$end = $this->input->post('end_date');

		$orders = $this->database->GetOrdersForDate($start, $end);

		if($orders)
		{
			$gst_invoice_data = $this->get_gst_invoice_data($orders);	
		}
		

		display('sales', null);
	}	

	function  get_gst_invoice_data($orders)
	{
		//Get orders data for this month


		$arrayData = array(
							    array(NULL, 2010, 2011, 2012),
							    array('Q1',   12,   15,   21),
							    array('Q2',   56,   73,   86),
							    array('Q3',   52,   61,   69),
							    array('Q4',   30,   32,    0),
							);		

		$spreadsheet_data = array();
		_add_address_and_user_to_orders($orders);
		//var_dump($orders[0]);
		//var_dump($orders[0]['order_items']);
		//Traverse each order and input a row in the excel sheet with details as in clartax template
		foreach ($orders as $key => $order)
		{
			$order_items = $order['order_items'];
			foreach ($order_items as $key => $item)
			{
				$order_data = array();
				//Start building xcl data, needs to happen for every item
				$order_data[] = $order['date_created'];		//Invoice Date
				$order_data[] = "INV-".$order['txn_id'];	//Invoice Num
				$order_data[] = $order['address']['first_name'];	//Billing Name
				$order_data[] = '-';	//Customer GSTIN
				$order_data[] = $order['address']['state'];		//Place of supply
				$order_data[] = 'G';	//Goods or Servie?
				$order_data[] = $item['product']['product_type'];	//Descriptiom
				$order_data[] = '-';	//HSN SAC
				$order_data[] = $item['count'];	//Qty
				$order_data[] = '-';	//Unit of measurement
				$order_data[] = $item['product']['product_price'];	//Price
				$order_data[] = '-';	//Discount
				$order_data[] = $item['product']['product_price'];	//taxabl Value
				
				//Get GST values
				$gst_values = $this->_calculate_gst_values($item, $order['address']);

				$order_data[] = $gst_values['cgst_percent'];	//CGST Value
				$order_data[] = $gst_values['cgst_amount'];	//CGST Value
				$order_data[] = $gst_values['sgst_percent'];	//CGST Value
				$order_data[] = $gst_values['sgst_amount'];	//CGST Value
				$order_data[] = $gst_values['igst_percent'];	//CGST Value
				$order_data[] = $gst_values['igst_amount'];	//CGST Value
				$order_data[] = '-';	//cess rate
				$order_data[] = '-';	//cess amount
				$order_data[] = 'N';	//Bill of supply?
				$order_data[] = 'N';	//Nill rated?
				$order_data[] = 'N';	//Reverse Charge?
				$order_data[] = '-';	//Type of export
				$order_data[] = '-';	//Shipping Port Code
				$order_data[] = '-';	//Export Shipping bill num
				$order_data[] = '-';	//Export Shipping bill date
				$order_data[] = '-';	//Has GST/IDT TDS been deducted
				$order_data[] = $this->config->item('gstin');	//MY GSTIN
				$order_data[] = format_address($order['address']);	//Customer address
				$order_data[] = $order['address']['city'];	//Customer city
				$order_data[] = $order['address']['state'];	//Customer state
				$order_data[] = '-';	//Document cancelled?
				$order_data[] = '-';	//Filing month?
				$order_data[] = '-';	//Filing Quarter?
				$order_data[] = '-';	//Original INV date
				$order_data[] = '-';	//original inv number
				$order_data[] = '-';	//original customer GSTIN
				$order_data[] = '-';	//GSTIN of marketplace
				$order_data[] = '-';	//date of linked advance
				$order_data[] = '-';	//Voucher of linked advance something
				$order_data[] = '-';	//Adjustment amount of linked advcance something
				$order_data[] = $order['order_amount'];	//Final taxable value


		//Invoice Date	Invoice Number	Customer Billing Name	Customer Billing GSTIN	State Place of Supply	Is the item a GOOD (G) or SERVICE (S)	Item Description	HSN or SAC code	Item Quantity	Item Unit of Measurement	Item Rate	Total Item Discount Amount	Item Taxable Value	CGST Rate	CGST Amount	SGST Rate	SGST Amount	IGST Rate	IGST Amount	CESS Rate	CESS Amount	Is this a Bill of Supply?	Is this a Nil Rated/Exempt/NonGST item?	Is Reverse Charge Applicable?	Type of Export	Shipping Port Code - Export	Shipping Bill Number - Export	Shipping Bill Date - Export	Has GST/IDT TDS been deducted	My GSTIN	Customer Billing Address	Customer Billing City	Customer Billing State	Is this document cancelled?	Is the customer a Composition dealer or UIN registered?	Return Filing Month	Return Filing Quarter	Original Invoice Date (In case of amendment)	Original Invoice Number (In case of amendment)	Original Customer Billing GSTIN (In case of amendment)	GSTIN of Ecommerce Marketplace	Date of Linked Advance Receipt	Voucher Number of Linked Advance Receipt	Adjustment Amount of the Linked Advance Receipt	Total Transaction Value

			array_push($spreadsheet_data, $order_data);
			unset($order_data);

			}			
		}
			//How to write to excel file
		$spreadsheet = new Spreadsheet();
		$spreadsheet->getActiveSheet()->fromArray($spreadsheet_data);

		//Download it
		$this->_download($spreadsheet);
	}

	//Returns CGST SGST IGST % and values
	function _calculate_gst_values($order_item, $address)
	{
		$prod = $order_item['product'];
		$percentage = $amount = 0;
		
		//Detect % based on product type
		switch ($prod['product_type'])
		{
			case 'tshirt':
				$percentage = 5;
				break;
			case 'mugs':
				$percentage = 18;
				break;
			case 'posters':
				$percentage = 12;
				break;
		}

		$value = (($percentage/100)  * $prod['product_price']);

		//Detect GST type based on address state
		$state = $address['state'];

		$type = ($state === $this->config->item('gst_state')) ? 'intra' : 'inter';
		$gst_values = null;

		switch ($type)
		{
			case 'intra':
				$gst_values['cgst_percent'] = $percentage/2;
				$gst_values['cgst_amount'] = $value/2;
				$gst_values['sgst_percent'] = $percentage/2;
				$gst_values['sgst_amount'] = $value/2;
				$gst_values['igst_percent'] = 0;
				$gst_values['igst_amount'] = 0;
				break;

			case 'inter':
				$gst_values['cgst_percent'] = 0;
				$gst_values['cgst_amount'] = 0;
				$gst_values['sgst_percent'] = 0;
				$gst_values['sgst_amount'] = 0;
				$gst_values['igst_percent'] = $percentage;
				$gst_values['igst_amount'] = $value;
				break;
		}

		return $gst_values;

	}


	function _download($spreadsheet)
	{
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="01simple.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		$writer = new PhpOffice\PhpSpreadsheet\writer\Xlsx($spreadsheet);
		$writer->save('php://output');
		exit;

	}

}



?>