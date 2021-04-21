<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><title>Order Id: <?php echo $order_d?> shipped!</title></head>
<body>
<div style="max-width: 800px; margin: 0; padding: 30px 0;">
<h3 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;"><?php echo $username 
?>, your order id : <?php echo $order_id ?> has been shipped! </h3>
<br>
<p>Our delivery minions are on their way to your shipping address with super cool awesomeness in their hands. We have advised them to not let their guard down as there might be dark and evil forces lurking in the wilderness, looking for some loot.</p>
<br>
<p>Anyway we have added a tracker on them so that you can track their movements. Here take a look.</p>
<strong><h2>Tracker Id : <a href="<?php echo $tracking_address ?>" style="color: #3366cc;"><?php echo $tracking_address ?></a></h2></strong>
<br />
<!--<big style="font: 16px/18px Arial, Helvetica, sans-serif;"><b><a href="<?php echo site_url(''); ?>" style="color: #3366cc;">Go to <?php echo $site_name; ?> now!</a></b></big><br />-->
<br />
<p>Please check the package thoroughly before accepting and signing. If the package is damaged in any way, please do not accept it and get in touch with us immediately.</p>
<br />
<br />
<br />
<?php  echo $this->load->view('email/signature', null, True) ?>;

</div>
</body>
</html>