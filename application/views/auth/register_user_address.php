<?php
if ($use_username) {
	$username = array(
		'name'	=> 'username',
		'id'	=> 'username',
		'value' => set_value('username'),
		'maxlength'	=> $this->config->item('username_max_length', 'tank_auth'),
		'size'	=> 30,
	);
}
$email = array(
	'name'	=> 'email',
	'id'	=> 'email',
	'value'	=> set_value('email'),
	'maxlength'	=> 80,
	'size'	=> 30,
);
$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
	'value' => set_value('password'),
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size'	=> 30,
);
$confirm_password = array(
	'name'	=> 'confirm_password',
	'id'	=> 'confirm_password',
	'value' => set_value('confirm_password'),
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size'	=> 30,
);
$first_name = array(
	'name'	=> 'first_name',
	'id'	=> 'first_name',
	'value' => set_value('first_name'),
	'maxlength'	=> 50,
	'size'	=> 30,
);
$last_name = array(
	'name'	=> 'last_name',
	'id'	=> 'last_name',
	'value' => set_value('last_name'),
	'maxlength'	=> 50,
	'size'	=> 30,
);
$address1 = array(
	'name'	=> 'address1',
	'id'	=> 'address1',
	'value' => set_value('address1'),
	'maxlength'	=> 50,
	'size'	=> 30,
);
$address2 = array(
	'name'	=> 'address2',
	'id'	=> 'address2',
	'value' => set_value('address2'),
	'maxlength'	=> 50,
	'size'	=> 30,
);
$city = array(
	'name'	=> 'city',
	'id'	=> 'city',
	'value' => set_value('city'),
	'maxlength'	=> 20,
	'size'	=> 30,
);
$state = array(
	'name'	=> 'state',
	'id'	=> 'state',
	'value' => set_value('state'),
	'maxlength'	=> 20,
	'size'	=> 30,
);
$country = array(
	'name'	=> 'country',
	'id'	=> 'country',
	'value' => set_value('country'),
	'maxlength'	=> 20,
	'size'	=> 30,
);
$pincode = array(
	'name'	=> 'pincode',
	'id'	=> 'pincode',
	'value' => set_value('pincode'),
	'maxlength'	=> 10,
	'size'	=> 30,
);
$number = array(
	'name'	=> 'number',
	'id'	=> 'number',
	'value' => set_value('number'),
	'maxlength'	=> 10,
	'size'	=> 30,
);
$captcha = array(
	'name'	=> 'captcha',
	'id'	=> 'captcha',
	'maxlength'	=> 8,
);
?>
<!--<?php echo form_open($this->uri->uri_string()); ?>-->
<div class="container top-bottom-space">
	<h1>Register</h1>
	<hr>
	<div class="well">
<?php echo form_open('auth/register_user_address'); ?>
<table>
	<?php if ($use_username) { ?>
	<tr>
		<td><?php echo form_label('Username', $username['id']); ?></td>
		<td><?php echo form_input($username); ?></td>
		<td style="color: red;"><?php echo form_error($username['name']); ?><?php echo isset($errors[$username['name']])?$errors[$username['name']]:''; ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td><?php echo form_label('Email Address', $email['id']); ?></td>
		<td><?php echo form_input($email); ?></td>
		<td style="color: red;"><?php echo form_error($email['name']); ?><?php echo isset($errors[$email['name']])?$errors[$email['name']]:''; ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('Password', $password['id']); ?></td>
		<td><?php echo form_password($password); ?></td>
		<td style="color: red;"><?php echo form_error($password['name']); ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('Confirm Password', $confirm_password['id']); ?></td>
		<td><?php echo form_password($confirm_password); ?></td>
		<td style="color: red;"><?php echo form_error($confirm_password['name']); ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('First Name', $first_name['id']); ?></td>
		<td><?php echo form_input($first_name); ?></td>
		<td style="color: red;"><?php echo form_error($first_name['name']); ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('Last Name', $last_name['id']); ?></td>
		<td><?php echo form_input($last_name); ?></td>
		<td style="color: red;"><?php echo form_error($last_name['name']); ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('Address1', $address1['id']); ?></td>
		<td><?php echo form_input($address1); ?></td>
		<td style="color: red;"><?php echo form_error($address1['name']); ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('Address2', $address2['id']); ?></td>
		<td><?php echo form_input($address2); ?></td>
		<td style="color: red;"><?php echo form_error($address2['name']); ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('City', $city['id']); ?></td>
		<td><?php echo form_input($city); ?></td>
		<td style="color: red;"><?php echo form_error($city['name']); ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('State', $state['id']); ?></td>
		<td><?php echo form_input($state); ?></td>
		<td style="color: red;"><?php echo form_error($state['name']); ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('Country', $country['id']); ?></td>
		<td><?php echo form_input($country); ?></td>
		<td style="color: red;"><?php echo form_error($country['name']); ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('Pin Code', $pincode['id']); ?></td>
		<td><?php echo form_input($pincode); ?></td>
		<td style="color: red;"><?php echo form_error($pincode['name']); ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('Number', $number['id']); ?></td>
		<td><?php echo form_input($number); ?></td>
		<td style="color: red;"><?php echo form_error($number['name']); ?></td>
	</tr>	
	<?php if ($captcha_registration) {
		if ($use_recaptcha) { ?>
	<tr>
		<td colspan="2">
			<div id="recaptcha_image"></div>
		</td>
		<td>
			<a href="javascript:Recaptcha.reload()">Get another CAPTCHA</a>
			<div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type('audio')">Get an audio CAPTCHA</a></div>
			<div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type('image')">Get an image CAPTCHA</a></div>
		</td>
	</tr>
	<tr>
		<td>
			<div class="recaptcha_only_if_image">Enter the words above</div>
			<div class="recaptcha_only_if_audio">Enter the numbers you hear</div>
		</td>
		<td><input type="text" id="recaptcha_response_field" name="recaptcha_response_field" /></td>
		<td style="color: red;"><?php echo form_error('recaptcha_response_field'); ?></td>
		<?php echo $recaptcha_html; ?>
	</tr>
	<?php } else { ?>
	<tr>
		<td colspan="3">
			<p>Enter the code exactly as it appears:</p>
			<?php echo $captcha_html; ?>
		</td>
	</tr>
	<tr>
		<td><?php echo form_label('Confirmation Code', $captcha['id']); ?></td>
		<td><?php echo form_input($captcha); ?></td>
		<td style="color: red;"><?php echo form_error($captcha['name']); ?></td>
	</tr>
	<?php }
	} ?>
</table>
</div>
<button class="btn btn-primary" type="submit">Register</button>
<?php echo form_close(); ?>
</div>