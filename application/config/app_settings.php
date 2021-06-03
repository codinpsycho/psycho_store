<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


//Default meta tags info

$config['title'] = 'Gaming Geek Anime T shirts and Merchandise Online India | Psycho Store';
$config['description'] = 'Buy gaming, geek, anime t shirts and other merchandises like gaming and anime posters, geek coffee mugs, designer mobile covers online in India. Satisfy your inner geek! ';
$config['keywords'] = 't-shirt, tshirt, t shirt, shirt, tee, t, t-shirts, tshirts, t shirts, shirts, tees, ts, clothing, clothes, threads, wear, gift, gifts, hats, hat, beanies, beanie, gear, sweatshirt, hoodie, sweatshirts, hoodies, gamer, geek, hacker, nerd, computer, gamers, geeks, hackers, nerds, coder, coders, psychostore, psycho, store, psycho store, india, ';

$config['favico'] = 'images/favico.png';
$config['size_chart'] = 'images/size_chart.jpg';

$config['restock_date'] = '20th June';
$config['cod_charge'] = '60';
$config['auto_discounts'] = false;

//Admin Panel
$config['admin_email'] = array('ishkaran.fearme@gmail.com', 'ishkaran.singh@hotmail.com', 'pekoj@getairmail.com');

//Partner Email
$config['partner_email'] = array('ishkaran.fearme@gmail.com', 'abhishek@gamexs.in');

//Mailgun and Newsletter
$config['mailgun_key'] = 'key-58a89ae28f19d2c9f072e26df633c80c';	//new one
$config['newsletter_address'] = 'update@news.psychostore.in';
$config['campaign_id'] = 'psycho_campaign';

$config['return_address'] = array(
									'first_name' => 'Ishkaran',
									'last_name' => 'Singh',
									'address_1' 	=> 'B274, Ranka Colony, Bannerghatta Road',
									'address_2' => 'Bilekahalli',
									'city' 	=> 'Bengaluru',
									'country' => 'India',
									'phone_number' 	=> '7387045828',
									'pincode' 	=> '560076',
									'state' 	=> 'Karnataka'
									);

/*Current status of the site
Possible Status :
LIVE
DOWN
TRAVELLING*/
$config['current_site_status'] = 'LIVE';

$config['gstin'] = '29AAICP0480E1ZF';
$config['gstin_state'] = 'Karnataka';


// dev on 28.05.2021
$config['redeem_points'] = false;

?>