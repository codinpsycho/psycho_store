<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
// $route['default_controller'] = 'welcome';
// $route['404_override'] = '';
// $route['translate_uri_dashes'] = FALSE;

$route['default_controller'] = "pages";
$route['product/(:any)/?(:any)?'] = 'pages/product/$1';
$route['like'] = 'pages/like';
$route['psycho_offers'] = 'pages/psycho_offers';
// $route['like/(:any)'] = 'pages/like/$1';
$route['explore/(:any)/?(:any)?'] = 'pages/explore/$1/$2';
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

$route['category/(:any)/products'] = 'pages/category_products/$1';

$route['admin/dashboard'] = 'insights/dashboard';
$route['admin'] = 'insights/dashboard';
$route['checkout/address/edit/(:num)'] = 'auth/address_edit/$1';

// like route revamped with categorised games for better SEO results: dev on 01.06.2021
$route['like/(:any)'] = 'pages/categorised_games/$1';
