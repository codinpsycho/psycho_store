<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/foo
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/
$route['default_controller'] = "pages";
$route['product/(:any)'] = 'pages/product/$1';
$route['like'] = 'pages/like';
$route['psycho_offers'] = 'pages/psycho_offers';
$route['like/(:any)'] = 'pages/like/$1';
$route['explore/(:any)'] = 'pages/explore/$1';
$route['latest'] = 'pages/latest/';
$route['subscribe'] = 'pages/subscribe/';
$route['shipping_returns'] = 'pages/shipping_returns/';
$route['contact'] = 'pages/contact/';
$route['about'] = 'pages/about/';
$route['media'] = 'pages/media/';
$route['student_discount'] = 'pages/student_discount/';
$route['popular'] = 'pages/popular/';
$route['feedback'] = 'pages/feedback/';
$route['giveaways'] = 'pages/giveaways/';
$route['coupon_partners'] = 'pages/coupon_partners/';
$route['beta'] = 'pages/beta/';
$route['404_override'] = '';


/* End of file routes.php */
/* Location: ./application/config/routes.php */
