<?php 
class Partner extends CI_controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('tank_auth');
		$this->load->library('table');
		$this->load->model('database');
		$this->load->helper('url');
		$this->load->helper('html');
		$this->load->helper('form');
		$this->load->helper('psycho_helper');
	}

	function index()
	{
		$this->inventory();
	}

	function _validate_user()
	{
		$current_user = $this->database->GetUserById($this->tank_auth->get_user_id());
		$valid_user = false;
		$admin_emails = $this->config->item('partner_email');

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
			redirect('auth/login/?redirect_url=partner');
		}
	}	

	//Displays previous orders for currently logged in user
	function inventory()
	{
		$this->_validate_user();

		$product_type = 'all' ;
		$game = 'all' ;
		$sort = 'latest';
		
		$products = $this->database->GetProducts($product_type, $sort, $game);
		
		$data['products'] = $products;
		$data['num_prods'] = count($products);
		$data['products_table'] = $this->_generate_products_table_for_partner($products);

		display('partner', $data);
	}

	function _generate_products_table_for_partner($products)
	{
		$this->load->library('table');
		$this->table->set_heading('type', 'game', 'name', 'url', 'image', 'price', 'small', 'med', 'lrg', 'xl');

		$tmpl = array ( 'table_open'  => '<table class="table table-condensed" >' );
		$this->table->set_template($tmpl);

		foreach ($products as $key => $prod)
		{
			//Product Image
			$img_path = site_url($prod['product_image_path']);
			$image_cell = "<a href= $img_path><img class='img-responsive' width='75' src = $img_path></img></a>";

			//Product Link			
			$prod_url = product_url($prod);
			$prod_name_cell = anchor($prod_url, $prod['product_name']);

			$this->table->add_row($prod['product_type'], $prod['product_game'], $prod_name_cell, $prod['product_url'], $image_cell, $prod['product_price'], $prod['product_count_small'], $prod['product_count_medium'], $prod['product_count_large'], $prod['product_count_xl']);
		}

		return $this->table->generate();
	}	
}
?>