<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//Include Google client library 
require APPPATH.'third_party/googleapi-php/Google_Client.php';
require APPPATH.'third_party/googleapi-php/contrib/Google_Oauth2Service.php';

/*
 * Configuration and setup Google API
 */

function init_google_client()
{

	$clientId = '858976915070-f2higqaj6iu3gpuijmddhqs973ihlf9m.apps.googleusercontent.com'; //Google client ID
	$clientSecret = 'OC_sWSagr5XX-Kapv8Tlb8Yx'; //Google client secret
	// $redirectURL = 'http://localhost/psycho_store/auth/external_auth'; //Callback URL
	$redirectURL = base_url().'/auth/external_auth'; //Callback URL

	//Call Google API
	$google_client = new Google_Client();
	$google_client->setApplicationName('Psycho Store Login');
	$google_client->setClientId($clientId);
	$google_client->setClientSecret($clientSecret);
	$google_client->setRedirectUri($redirectURL);
	$google_client->setScopes(array('https://www.googleapis.com/auth/userinfo.email','https://www.googleapis.com/auth/userinfo.profile'));

	return	$google_client;
}

function get_user_info($code)
{
	$google_client = init_google_client();
	$google_oauth_v2 = new Google_Oauth2Service($google_client);
	$google_client->authenticate($code);

	//Get user profile data from google
	
	$user_profile = $google_oauth_v2->userinfo->get();

	$user_info['email'] = $user_profile['email'];
	$user_info['username'] = $user_profile['name'];

	return $user_info;
}

function save_redirect_url($redirect_url)
{
	$ci = &get_instance();
	$ci->load->library('session');

	$ci->session->set_userdata('redirect_url', $redirect_url);
	$session_data = $ci->session->all_userdata();	
}

//Return redirect_url and clears it from the session
function clear_and_get_redirect_url()
{
	$ci = &get_instance();
	$ci->load->library('session');
	$session_data = $ci->session->all_userdata();

	$redirect_url = $ci->session->userdata('redirect_url');
	$ci->session->unset_userdata('redirect_url');

	if($redirect_url == false)
	{
		//Get it from GET
		$redirect_url = $ci->input->get('redirect_url');
	}
	return $redirect_url;
}

?>