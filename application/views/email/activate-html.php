<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><title>Welcome to <?php echo $site_name; ?>!</title></head>
<body>
<div style="max-width: 800px; margin: 0; padding: 30px 0;">
<table width="80%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="5%"></td>
<td align="left" width="95%" style="font: 13px/18px Arial, Helvetica, sans-serif;">
<h2 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;">Hey,  <?php echo $username; ?>!</h2>
Prove our minions that you are not here to scare them and you won't eat their bananas. All you have to do is click on the link below and our minions will be happy to work on your orders.
<br />
<br />
<big style="font: 16px/18px Arial, Helvetica, sans-serif;"><b><a href="<?php echo site_url('/auth/activate/'.$user_id.'/'.$new_email_key); ?>" style="color: #3366cc;">Minions, I won't scare you, or eat your bananas.</a></b></big><br />
<br />
Link doesn't work? Copy the following link to your browser address bar:<br />
<nobr><a href="<?php echo site_url('/auth/activate/'.$user_id.'/'.$new_email_key); ?>" style="color: #3366cc;"><?php echo site_url('/auth/activate/'.$user_id.'/'.$new_email_key); ?></a></nobr><br />
<br />
Please verify your email within <?php echo $activation_period; ?> hours, otherwise your registration will become invalid and you will have to register again. Also our doubts about you will be proven right.<br />
<br />
<br />
Thanks for joining <?php echo $site_name; ?>. Here's your signin details.
<br />
<?php if (strlen($username) > 0) { ?>Your username: <?php echo $username; ?><br /><?php } ?>
Your email address: <?php echo $email; ?><br />
<?php if (isset($password)) { /* ?>Your password: <?php echo $password; ?><br /><?php */ } ?>
<br />
<br />
<?php  echo $this->load->view('email/signature', null, True) ?>;
</td>
</tr>
</table>
</div>
</body>
</html>