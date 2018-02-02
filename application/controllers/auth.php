<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->helper(array('form', 'url', 'html'));
		$this->load->library('form_validation');
		$this->load->library('security');
		$this->load->library('tank_auth');
		$this->lang->load('tank_auth');
		$this->load->model('database');
		$this->load->model('tank_auth/users');
		$this->load->library('cart');
		$this->load->helper('psycho_helper');
		$this->load->helper('mailgun_helper');
		$this->load->helper('auth_helper');
	}

	function index()
	{
		if ($message = $this->session->flashdata('message')) 
		{
			$data['heading'] = 'Message';
			$data['content'] = $message;
			display('message',$data);
		} 
		else
		{
			redirect('/auth/login/');
		}
	}

	//Checks for user info either in POST or GET(google)
	function _process_user_info()
	{
		$user_info = null;
		$code = $this->input->get('code');

		//Google redirects with code insead of post
		if( $code )
		{
			$user_info = get_user_info( $this->input->get('code') );

		}
		else
		{

			$user_info['email'] = trim($this->input->post('email'));
			$user_info['username'] = trim($this->input->post('username'));
		}

		return $user_info;
	}

	function external_auth()
	{
		/*Improvements to do 
		1. Check for valid email_id, notify otherwise
		2. Check for unique username, if not then ask for a username
		*/
		//Check for get(google auth) as most of the logins should be that only
		//If not, check for post(fb and normal)

		$user_info = $this->_process_user_info();
		//var_dump($this->input->get('code'));
			
		$email =  $user_info['email'];//trim($this->input->post('email'));
		$username = $user_info['username']; //trim($this->input->post('username'));
		
		if($this->users->is_email_available($email))
		{
			//First time fb_Login, create new user
			$this->_external_register($email, $username);
		}

		//get the user by email
		$user = $this->users->get_user_by_email($email);

		//Sign-in
		$this->_external_login($user->id, $user->username);
	}

	function _external_login($user_id, $username)
	{
		// simulate what happens in the tank auth
		$this->session->set_userdata(array(	'user_id' => $user_id, 'username' => $username,
											'status' => STATUS_ACTIVATED));
		
		//$this->tank_auth->clear_login_attempts($user->email);
		
		$this->users->update_login_info( $user_id, $this->config->item('login_record_ip', 'tank_auth'), 
										 $this->config->item('login_record_time', 'tank_auth'));

		$this->_redirect();
	}

	function _external_register($_email, $_username)
	{
		$username   =   $_username;//trim($this->input->post('username'));
		$email      =   $_email;//trim($this->input->post('email'));
		
		$password = $this->_generate_password(9, 8);
		$user = $this->tank_auth->create_user($username, $email, $password, false);

		//Add it to subscribers list
		//add_subscriber($email, $username);

		return $user;
	}

	function _redirect()
	{
		$redirect_url = clear_and_get_redirect_url();
		//$redirect_url = $url != (string)FALSE ? $redirect_url = $url : '';

		$this->_post_login($redirect_url);
	}

	/**
	 * Login user on the site
	 *
	 * @return void
	 */
	function login()
	{
		if ($this->tank_auth->is_logged_in()) {									// logged in
			redirect('');

		} elseif ($this->tank_auth->is_logged_in(FALSE)) {						// logged in, not activated
			redirect('/auth/send_again/');

		} else {
			$data['login_by_username'] = ($this->config->item('login_by_username', 'tank_auth') AND
					$this->config->item('use_username', 'tank_auth'));
			$data['login_by_email'] = $this->config->item('login_by_email', 'tank_auth');

			$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
			$this->form_validation->set_rules('remember', 'Remember me', 'integer');

			// Get login for counting attempts to login
			if ($this->config->item('login_count_attempts', 'tank_auth') AND
					($login = $this->input->post('login'))) {
				$login = $this->security->xss_clean($login);
			} else {			
				$login = '';
			}

			$data['use_recaptcha'] = $this->config->item('use_recaptcha', 'tank_auth');
			if ($this->tank_auth->is_max_login_attempts_exceeded($login)) {
				if ($data['use_recaptcha'])
					$this->form_validation->set_rules('recaptcha_response_field', 'Confirmation Code', 'trim|xss_clean|required|callback__check_recaptcha');
				else
					$this->form_validation->set_rules('captcha', 'Confirmation Code', 'trim|xss_clean|required|callback__check_captcha');
			}
			$data['errors'] = array();
						
			if ($this->form_validation->run()) 
			{
				// validation ok
				if ($this->tank_auth->login(
						$this->form_validation->set_value('email'),
						$this->form_validation->set_value('password'),
						$this->form_validation->set_value('remember'),
						$data['login_by_username'],
						$data['login_by_email'])) 
						{
							$this->_redirect();
				}
				else
				{
					$errors = $this->tank_auth->get_error_message();
					if (isset($errors['banned'])) {								// banned user
						$this->_show_message($this->lang->line('auth_message_banned').' '.$errors['banned']);

					} elseif (isset($errors['not_activated'])) {				// not activated user
						redirect('/auth/send_again/');

					} else {													// fail
						foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
					}					
				}
			}			
			
			$data['show_captcha'] = FALSE;
			if ($this->tank_auth->is_max_login_attempts_exceeded($login)) {
				$data['show_captcha'] = TRUE;
				if ($data['use_recaptcha']) {
					$data['recaptcha_html'] = $this->_create_recaptcha();
				} else {
					$data['captcha_html'] = $this->_create_captcha();
				}
			}
			//$this->load->view('auth/login_form', $data);
			$data['meta_id'] = 7;

			//required for  google auth flow ----
			$gclient = init_google_client();
			$data['gauth_url'] = $gclient->createAuthUrl();
			//till here ----

			save_redirect_url($this->input->get('redirect_url'));
			$sess_url = $this->session->userdata('redirect_url');

			display('login',$data);
		}
	}


	function _post_login($redirect_url)
	{
		$username = $this->tank_auth->get_username();
		$params['title'] = "Greetings $username";		
		notify_event('login_done', $params);
		redirect($redirect_url);
	}

	function saysomething()
	{
		//Form validation
		$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean|min_length['.$this->config->item('username_min_length', 'tank_auth').']|max_length['.$this->config->item('username_max_length', 'tank_auth').']');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');
		$this->form_validation->set_rules('msg', 'Message', 'trim|required|xss_clean');
		
		$data['def_username'] = '';
		$data['def_email'] = '';

		if($this->tank_auth->is_logged_in())
		{
			$user = $this->database->GetUserById($this->tank_auth->get_user_id());
			$data['def_username'] = $user['username'];
			$data['def_email'] = $user['email'];
		}

		if($this->form_validation->run())
		{
			//validation done, add feedback to db
			$feedback['name'] = $this->form_validation->set_value('username');
			$feedback['email'] = $name = $this->form_validation->set_value('email');
			$feedback['message'] = $name = $this->form_validation->set_value('msg');
			$this->database->AddFeedback($feedback);
			$this->_show_message($this->lang->line('auth_message_got_feedback').heading(anchor('feedback', 'Feedback Wall'),3));
		}

		display('feedback_form', $data);
	}


	/**
	 * Logout user
	 *
	 * @return void
	 */
	function logout()
	{
		$this->tank_auth->logout();
		
		//Unset required session vars
		//$this->session->unset_userdata('txn_id');
		$this->cart->remove_discount();
		$this->_redirect();
		//$this->_show_message($this->lang->line('auth_message_logged_out'));
	}

	/**
	 * Register user on the site, without any address
	 *
	 * @return void
	 */
	function register()
	{
		if ($this->tank_auth->is_logged_in()) {									// logged in
			redirect('');

		} elseif ($this->tank_auth->is_logged_in(FALSE)) {						// logged in, not activated
			redirect('/auth/send_again/');

		} elseif (!$this->config->item('allow_registration', 'tank_auth')) {	// registration is off
			$this->_show_message($this->lang->line('auth_message_registration_disabled'));

		} else {
			 $use_username = $this->config->item('use_username', 'tank_auth');
			if ($use_username) 
			{				
				$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean|min_length['.$this->config->item('username_min_length', 'tank_auth').']|max_length['.$this->config->item('username_max_length', 'tank_auth').']');
			}
			$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|min_length['.$this->config->item('password_min_length', 'tank_auth').']|max_length['.$this->config->item('password_max_length', 'tank_auth').']');
			//$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|xss_clean|matches[password]');

			$captcha_registration	= $this->config->item('captcha_registration', 'tank_auth');
			$use_recaptcha			= $this->config->item('use_recaptcha', 'tank_auth');
			if ($captcha_registration) {
				if ($use_recaptcha) {
					$this->form_validation->set_rules('recaptcha_response_field', 'Confirmation Code', 'trim|xss_clean|required|callback__check_recaptcha');
				} else {
					$this->form_validation->set_rules('captcha', 'Confirmation Code', 'trim|xss_clean|required|callback__check_captcha');
				}
			}
			$data['errors'] = array();

			$email_activation = $this->config->item('email_activation', 'tank_auth');

			if ($this->form_validation->run())
			{								// validation ok
				if (!is_null($data = $this->tank_auth->create_user(
						$use_username ? $this->form_validation->set_value('username') : '',
						$this->form_validation->set_value('email'),
						$this->form_validation->set_value('password'),
						$email_activation)))
				{
					//success					
					$data['site_name'] = $this->config->item('website_name', 'tank_auth');
					if ($email_activation)
					{									// send "activate" email
						$data['activation_period'] = $this->config->item('email_activation_expire', 'tank_auth') / 3600;

						$this->_send_email('activate', $data['email'], $data);

						unset($data['password']); // Clear password (just for any case)

						$this->_show_message($this->lang->line('auth_message_registration_completed_1'));
					} 
					else
					{
						//Directly login the user, that's it, no activation nonsense (for nows)
						$this->_external_login($data['user_id'], $this->form_validation->set_value('username') );
						
						// if ($this->config->item('email_account_details', 'tank_auth')) {	// send "welcome" email

						// 	$this->_send_email('welcome', $data['email'], $data);
						// }
						// unset($data['password']); // Clear password (just for any case)

						// $this->_show_message($this->lang->line('auth_message_registration_completed_2').'   '.anchor('/auth/login/', 'Login'));
					}
				}
				else
				{					
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	
					{						
						$data['errors'][$k] = $this->lang->line($v);						
					}		
				}
			}				
			if ($captcha_registration) {
				if ($use_recaptcha) {
					$data['recaptcha_html'] = $this->_create_recaptcha();
				} else {
					$data['captcha_html'] = $this->_create_captcha();
				}
			}
			$data['use_username'] = $use_username;
			$data['captcha_registration'] = $captcha_registration;
			$data['use_recaptcha'] = $use_recaptcha;
			display('register_user_address', $data);
		}
	}


	/**
	 * Register user on the site, with any address
	 *
	 * @return void
	 */
	function register_user_address()
	{
		if ($this->tank_auth->is_logged_in()) {									// logged in
			redirect('');

		} elseif ($this->tank_auth->is_logged_in(FALSE)) {						// logged in, not activated
			redirect('/auth/send_again/');

		} elseif (!$this->config->item('allow_registration', 'tank_auth')) {	// registration is off
			$this->_show_message($this->lang->line('auth_message_registration_disabled'));

		} else {
			$use_username = $this->config->item('use_username', 'tank_auth');
			if ($use_username) {
				$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean|min_length['.$this->config->item('username_min_length', 'tank_auth').']|max_length['.$this->config->item('username_max_length', 'tank_auth').']|alpha_dash');
			}
			$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');
			$this->form_validation->set_rules('first_name', 'First Name', 'trim|required|xss_clean');
			$this->form_validation->set_rules('last_name', 'Last Name', 'trim|required|xss_clean');
			$this->form_validation->set_rules('address1', 'Address1', 'trim|required|xss_clean');
			$this->form_validation->set_rules('address2', 'Address2', 'trim|xss_clean');
			$this->form_validation->set_rules('city', 'City', 'trim|required|xss_clean');
			$this->form_validation->set_rules('state', 'State', 'trim|required|xss_clean');
			$this->form_validation->set_rules('country', 'Country', 'trim|required|xss_clean');
			$this->form_validation->set_rules('pincode', 'Pin Code', 'trim|required|xss_clean');
			$this->form_validation->set_rules('number', 'Number', 'trim|required|xss_clean');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|min_length['.$this->config->item('password_min_length', 'tank_auth').']|max_length['.$this->config->item('password_max_length', 'tank_auth').']');
			$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|xss_clean|matches[password]');

			$captcha_registration	= $this->config->item('captcha_registration', 'tank_auth');
			$use_recaptcha			= $this->config->item('use_recaptcha', 'tank_auth');
			if ($captcha_registration) {
				if ($use_recaptcha) {
					$this->form_validation->set_rules('recaptcha_response_field', 'Confirmation Code', 'trim|xss_clean|required|callback__check_recaptcha');
				} else {
					$this->form_validation->set_rules('captcha', 'Confirmation Code', 'trim|xss_clean|required|callback__check_captcha');
				}
			}
			$data['errors'] = array();

			$email_activation = $this->config->item('email_activation', 'tank_auth');

			if ($this->form_validation->run()) {								// validation ok
				if (!is_null($data = $this->tank_auth->create_user(
						$use_username ? $this->form_validation->set_value('username') : '',
						$this->form_validation->set_value('email'),
						$this->form_validation->set_value('password'),
						$email_activation))) {									// success

					//Create an entry into address table as well
					 $address = array
					(
						'first_name' => $this->form_validation->set_value('first_name'),
						'last_name' => $this->form_validation->set_value('last_name'),
						'address_1' => $this->form_validation->set_value('address1'),
						'address_2' => $this->form_validation->set_value('address2'),
						'city' => $this->form_validation->set_value('city'),
						'state' => $this->form_validation->set_value('state'),
						'country' => $this->form_validation->set_value('country'),
						'pincode' => $this->form_validation->set_value('pincode'),
						'phone_number' => $this->form_validation->set_value('number'),
						'user_id' => $data['user_id']
					);					
					$this->database->RegisterAddress($address);
					$this->database->Subscribe($data['email']);

					$data['site_name'] = $this->config->item('website_name', 'tank_auth');

					if ($email_activation) {									// send "activate" email
						$data['activation_period'] = $this->config->item('email_activation_expire', 'tank_auth') / 3600;

						$this->_send_email('activate', $data['email'], $data);

						unset($data['password']); // Clear password (just for any case)

						$this->_show_message($this->lang->line('auth_message_registration_completed_1'));

					} else {
						if ($this->config->item('email_account_details', 'tank_auth')) {	// send "welcome" email

							$this->_send_email('welcome', $data['email'], $data);
						}						
						unset($data['password']); // Clear password (just for any case)

						$this->_show_message($this->lang->line('auth_message_registration_completed_2').' echo br()'.anchor('/auth/login/', 'Login'));
					}
				}
				else
				{					
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			if ($captcha_registration) {
				if ($use_recaptcha) {
					$data['recaptcha_html'] = $this->_create_recaptcha();
				} else {
					$data['captcha_html'] = $this->_create_captcha();
				}
			}
			$data['use_username'] = $use_username;
			$data['captcha_registration'] = $captcha_registration;
			$data['use_recaptcha'] = $use_recaptcha;
			//$this->load->view('auth/register_user_address', $data);
			$data['meta_id'] = 8;
			display('register_user_address', $data);
		}
	}

	function remove_address($address_id)
	{
		$this->database->RemoveAddress($address_id);
		redirect('checkout/address');
	}

	function _show_address_detection_popup()
	{
		show_alert("Our satellites are trying to pin point your location. Please dont move and select share/allow on the top left pop up." );
	}

	function _detect_address()
	{
		notify_event('geolocation');
	}

	function register_address()
	{		
		if (!$this->tank_auth->is_logged_in())			// logged in
			redirect('auth/login');
		elseif ($this->tank_auth->is_logged_in(FALSE)) 	// logged in, not activated
			redirect('/auth/send_again/');
		else 
		{
			//Run address detection script
			//$this->_show_address_detection_popup();
			//$this->_detect_address();

			$this->form_validation->set_rules('first_name', 'First Name', 'trim|required|xss_clean');
			$this->form_validation->set_rules('last_name', 'Last Name', 'trim|required|xss_clean');
			$this->form_validation->set_rules('address1', 'Address1', 'trim|required|xss_clean');
			$this->form_validation->set_rules('address2', 'Address2', 'trim|xss_clean');
			$this->form_validation->set_rules('address3', 'Address3', 'trim|xss_clean');
			$this->form_validation->set_rules('city', 'City', 'trim|required|xss_clean');
			$this->form_validation->set_rules('state', 'State', 'trim|required|xss_clean');
			//$this->form_validation->set_rules('country', 'Country', 'trim|required|xss_clean');	//Its India for now
			$this->form_validation->set_rules('pincode', 'Pin Code', 'trim|required|xss_clean|min_length[6]||max_length[6]');
			$this->form_validation->set_rules('number', 'Number', 'trim|required|xss_clean|min_length[10]|max_length[10]');

			if ($this->form_validation->run())
			{
				// validation ok
				//Create an entry into address table as well
				 $address = array
				(
					'first_name' => $this->form_validation->set_value('first_name'),
					'last_name' => $this->form_validation->set_value('last_name'),
					'address_1' => $this->form_validation->set_value('address1'),
					'address_2' => $this->form_validation->set_value('address2'),
					'address_3' => $this->form_validation->set_value('address3'),
					'city' => $this->form_validation->set_value('city'),
					'state' => $this->form_validation->set_value('state'),
					'country' => 'India',	//$this->form_validation->set_value('country'),
					'pincode' => $this->form_validation->set_value('pincode'),
					'phone_number' => $this->form_validation->set_value('number'),
					'user_id' => $this->tank_auth->get_user_id()
				);
				$this->database->RegisterAddress($address);
				redirect('checkout/address');
			}
			//$this->load->view('auth/add_address');
			display('add_address',null);
		}
	}

	/**
	 * Send activation email again, to the same or new email address
	 *
	 * @return void
	 */
	function send_again()
	{
		if (!$this->tank_auth->is_logged_in(FALSE)) {							// not logged in or activated
			redirect('/auth/login/');

		} else {
			$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if (!is_null($data = $this->tank_auth->change_email(
						$this->form_validation->set_value('email')))) {			// success

					$data['site_name']	= $this->config->item('website_name', 'tank_auth');
					$data['activation_period'] = $this->config->item('email_activation_expire', 'tank_auth') / 3600;

					$this->_send_email('activate', $data['email'], $data);

					$this->_show_message(sprintf($this->lang->line('auth_message_activation_email_sent'), $data['email']));

				} else {
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			display('send_again', $data);			
		}
	}	

	/**
	 * Activate user account.
	 * User is verified by user_id and authentication code in the URL.
	 * Can be called by clicking on link in mail.
	 *
	 * @return void
	 */
	function activate()
	{
		$user_id		= $this->uri->segment(3);
		$new_email_key	= $this->uri->segment(4);

		// Activate user
		if ($this->tank_auth->activate_user($user_id, $new_email_key))
		{
			// success
			//Add it into newletter
			$user = $this->database->GetUserById($user_id);
			add_subscriber($user['email'], $user['username']);
			
			$this->tank_auth->logout();
			$this->_show_message($this->lang->line('auth_message_activation_completed').' '.anchor('/auth/login/', 'Login'));
		}
		else
		{
			// fail
			$this->_show_message($this->lang->line('auth_message_activation_failed'));
		}
	}

	/**
	 * Generate reset code (to change password) and send it to user
	 *
	 * @return void
	 */
	function forgot_password()
	{
		if ($this->tank_auth->is_logged_in()) {									// logged in
			redirect('');

		} elseif ($this->tank_auth->is_logged_in(FALSE)) {						// logged in, not activated
			redirect('/auth/send_again/');

		} else {
			$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if (!is_null($data = $this->tank_auth->forgot_password(
						$this->form_validation->set_value('email')))) {

					$data['site_name'] = $this->config->item('website_name', 'tank_auth');

					// Send email with password activation link
					$this->_send_email('forgot_password', $data['email'], $data);

					$this->_show_message($this->lang->line('auth_message_new_password_sent'));

				} else {
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			//$this->load->view('auth/forgot_password_form', $data);
			display('forgot_password', $data);
		}
	}

	/**
	 * Replace user password (forgotten) with a new one (set by user).
	 * User is verified by user_id and authentication code in the URL.
	 * Can be called by clicking on link in mail.
	 *
	 * @return void
	 */
	function reset_password()
	{
		$user_id		= $this->uri->segment(3);
		$new_pass_key	= $this->uri->segment(4);
		

		$this->form_validation->set_rules('new_password', 'New Password', 'trim|required|xss_clean|min_length['.$this->config->item('password_min_length', 'tank_auth').']|max_length['.$this->config->item('password_max_length', 'tank_auth').']');
		$this->form_validation->set_rules('confirm_new_password', 'Confirm new Password', 'trim|required|xss_clean|matches[new_password]');

		$data['errors'] = array();

		if ($this->form_validation->run()) {								// validation ok
			if (!is_null($data = $this->tank_auth->reset_password(
					$user_id, $new_pass_key,
					$this->form_validation->set_value('new_password')))) {	// success

				$data['site_name'] = $this->config->item('website_name', 'tank_auth');

				// Send email with new password
				$this->_send_email('reset_password', $data['email'], $data);

				$this->_show_message($this->lang->line('auth_message_new_password_activated').' '.anchor('/auth/login/', 'Login'));

			} else {				
				$this->_show_message($this->lang->line('auth_message_new_password_failed'));
			}
		} else {
			// Try to activate user by password key (if not activated yet)
			if ($this->config->item('email_activation', 'tank_auth')) {
				$this->tank_auth->activate_user($user_id, $new_pass_key, FALSE);
			}

			if (!$this->tank_auth->can_reset_password($user_id, $new_pass_key)) {				
				$this->_show_message($this->lang->line('auth_message_new_password_failed'));
			}
		}
		//$this->load->view('auth/reset_password_form', $data);
		display('reset_password', $data);
	}

	/**
	 * Change user password
	 *
	 * @return void
	 */
	function change_password()
	{
		if (!$this->tank_auth->is_logged_in()) {								// not logged in or not activated
			redirect('/auth/login/');

		} else {
			$this->form_validation->set_rules('old_password', 'Old Password', 'trim|required|xss_clean');
			$this->form_validation->set_rules('new_password', 'New Password', 'trim|required|xss_clean|min_length['.$this->config->item('password_min_length', 'tank_auth').']|max_length['.$this->config->item('password_max_length', 'tank_auth').']|alpha_dash');
			$this->form_validation->set_rules('confirm_new_password', 'Confirm new Password', 'trim|required|xss_clean|matches[new_password]');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if ($this->tank_auth->change_password(
						$this->form_validation->set_value('old_password'),
						$this->form_validation->set_value('new_password'))) {	// success
					$this->_show_message($this->lang->line('auth_message_password_changed'));

				} else {														// fail
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			$this->load->view('auth/change_password_form', $data);
		}
	}

	/**
	 * Change user email
	 *
	 * @return void
	 */
	function change_email()
	{
		if (!$this->tank_auth->is_logged_in()) {								// not logged in or not activated
			redirect('/auth/login/');

		} else {
			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if (!is_null($data = $this->tank_auth->set_new_email(
						$this->form_validation->set_value('email'),
						$this->form_validation->set_value('password')))) {			// success

					$data['site_name'] = $this->config->item('website_name', 'tank_auth');

					// Send email with new email address and its activation link
					$this->_send_email('change_email', $data['new_email'], $data);

					$this->_show_message(sprintf($this->lang->line('auth_message_new_email_sent'), $data['new_email']));

				} else {
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			$this->load->view('auth/change_email_form', $data);
		}
	}

	/**
	 * Replace user email with a new one.
	 * User is verified by user_id and authentication code in the URL.
	 * Can be called by clicking on link in mail.
	 *
	 * @return void
	 */
	function reset_email()
	{
		$user_id		= $this->uri->segment(3);
		$new_email_key	= $this->uri->segment(4);

		// Reset email
		if ($this->tank_auth->activate_new_email($user_id, $new_email_key)) {	// success
			$this->tank_auth->logout();
			$this->_show_message($this->lang->line('auth_message_new_email_activated').' '.anchor('/auth/login/', 'Login'));

		} else {																// fail
			$this->_show_message($this->lang->line('auth_message_new_email_failed'));
		}
	}

	/**
	 * Delete user from the site (only when user is logged in)
	 *
	 * @return void
	 */
	function unregister()
	{
		if (!$this->tank_auth->is_logged_in()) {								// not logged in or not activated
			redirect('/auth/login/');

		} else {
			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if ($this->tank_auth->delete_user(
						$this->form_validation->set_value('password'))) {		// success
					$this->_show_message($this->lang->line('auth_message_unregistered'));

				} else {														// fail
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			$this->load->view('auth/unregister_form', $data);
		}
	}

	/**
	 * Show info message
	 *
	 * @param	string
	 * @return	void
	 */
	function _show_message($message)
	{
		$this->session->set_flashdata('message', $message);
		redirect('/auth/');
	}

	/**
	 * Send email message of given type (activate, forgot_password, etc.)
	 *
	 * @param	string
	 * @param	string
	 * @param	array
	 * @return	void
	 */
	function _send_email($type, $email, &$data)
	{
		$params = mg_create_mail_params($type, $data);
		mg_send_mail($email, $params);		
	}

	/**
	 * Create CAPTCHA image to verify user as a human
	 *
	 * @return	string
	 */
	function _create_captcha()
	{
		$this->load->helper('captcha');

		$cap = create_captcha(array(
			'img_path'		=> './'.$this->config->item('captcha_path', 'tank_auth'),
			'img_url'		=> base_url().$this->config->item('captcha_path', 'tank_auth'),
			'font_path'		=> './'.$this->config->item('captcha_fonts_path', 'tank_auth'),
			'font_size'		=> $this->config->item('captcha_font_size', 'tank_auth'),
			'img_width'		=> $this->config->item('captcha_width', 'tank_auth'),
			'img_height'	=> $this->config->item('captcha_height', 'tank_auth'),
			'show_grid'		=> $this->config->item('captcha_grid', 'tank_auth'),
			'expiration'	=> $this->config->item('captcha_expire', 'tank_auth'),
		));

		// Save captcha params in session
		$this->session->set_flashdata(array(
				'captcha_word' => $cap['word'],
				'captcha_time' => $cap['time'],
		));

		return $cap['image'];
	}

	/**
	 * Callback function. Check if CAPTCHA test is passed.
	 *
	 * @param	string
	 * @return	bool
	 */
	function _check_captcha($code)
	{
		$time = $this->session->flashdata('captcha_time');
		$word = $this->session->flashdata('captcha_word');

		list($usec, $sec) = explode(" ", microtime());
		$now = ((float)$usec + (float)$sec);

		if ($now - $time > $this->config->item('captcha_expire', 'tank_auth')) {
			$this->form_validation->set_message('_check_captcha', $this->lang->line('auth_captcha_expired'));
			return FALSE;

		} elseif (($this->config->item('captcha_case_sensitive', 'tank_auth') AND
				$code != $word) OR
				strtolower($code) != strtolower($word)) {
			$this->form_validation->set_message('_check_captcha', $this->lang->line('auth_incorrect_captcha'));
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Create reCAPTCHA JS and non-JS HTML to verify user as a human
	 *
	 * @return	string
	 */
	function _create_recaptcha()
	{
		$this->load->helper('recaptcha');

		// Add custom theme so we can get only image
		$options = "<script>var RecaptchaOptions = {theme: 'custom', custom_theme_widget: 'recaptcha_widget'};</script>\n";

		// Get reCAPTCHA JS and non-JS HTML
		$html = recaptcha_get_html($this->config->item('recaptcha_public_key', 'tank_auth'));

		return $options.$html;
	}

	/**
	 * Callback function. Check if reCAPTCHA test is passed.
	 *
	 * @return	bool
	 */
	function _check_recaptcha()
	{
		$this->load->helper('recaptcha');

		$resp = recaptcha_check_answer($this->config->item('recaptcha_private_key', 'tank_auth'),
				$_SERVER['REMOTE_ADDR'],
				$_POST['recaptcha_challenge_field'],
				$_POST['recaptcha_response_field']);

		if (!$resp->is_valid) {
			$this->form_validation->set_message('_check_recaptcha', $this->lang->line('auth_incorrect_captcha'));
			return FALSE;
		}
		return TRUE;
	}

	// generates a random password for the user
	function _generate_password($length=9, $strength=0) 
	{
		$vowels = 'aeuy';
		$consonants = 'bdghjmnpqrstvz';
		if ($strength & 1) { $consonants .= 'BDGHJLMNPQRSTVWXZ'; }
		if ($strength & 2) { $vowels .= "AEUY"; }
		if ($strength & 4) { $consonants .= '23456789'; }
		if ($strength & 8) { $consonants .= '@#$%'; }
	 
		$password = '';
		$alt = time() % 2;
		for ($i = 0; $i < $length; $i++) 
		{
			if ($alt == 1) 
			{
				$password .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			} 
			else 
			{
				$password .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		return $password;
	}

}

/* End of file auth.php */
/* Location: ./application/controllers/auth.php */