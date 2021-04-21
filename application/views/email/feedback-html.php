<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><title><?php echo $username?>, Psycho Store says "Thank You"</title></head>
<body>
<div style="max-width: 800px; margin: 0; padding: 30px 0;">
<table width="80%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="5%"></td>
<td align="left" width="95%" style="font: 13px/18px Arial, Helvetica, sans-serif;">
<h2 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;"><?php echo $username?>, Psycho Store says "Thank You"</h2>

The minions who were responsible for handling your order have been imprisoned and we won't be giving them any bananas until and unless you confirm us you have fallen in love with your merchandise.
<br>
<br>
<a href="psychostore.in/auth/saysomething">Free the minions</a>.
<br>
<br>
<b>We would absolutely love to see you with your psychostore merchandise</b><br>
1. Click your pic with your merchandise.<br>
2. Share it with us.<br><br>
Leave the rest to us, we will make sure that you unlock some good discount on your next order.
<br>
<br>
<br>
<p>If there is any query/concern, do contact us at contact@psychostore.in</p>
<br>
<?php  echo $this->load->view('email/signature', null, True) ?>;
</td>
</tr>
</table>
</div>
</body>
</html>