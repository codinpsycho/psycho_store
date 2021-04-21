<div class="container top-bottom-space">
	<h1>Forgot Password</h1>
	<hr>
	<div class="well">
<?php
$login = array(
	'name'	=> 'email',
	'id'	=> 'email',
	'value' => set_value('email'),
	'maxlength'	=> 80,
	'size'	=> 30,
);
if ($this->config->item('use_username', 'tank_auth')) {
	$login_label = 'Email';
} else {
	$login_label = 'Email';
}
?>
<?php echo form_open($this->uri->uri_string()); ?>

<!-- 		<td><?php echo form_label($login_label, $login['id']); ?></td>
		<td><?php echo form_input($login); ?></td>
		<td style="color: red;"><?php echo form_error($login['name']); ?><?php echo isset($errors[$login['name']])?$errors[$login['name']]:''; ?></td>
 -->
 <div class="form-group">
    <?php echo $this->load->view('view_email', null, True) ?>
  </div>
</div>
<button class="btn btn-primary" type="submit">Get a New Password</button>
<?php echo form_close(); ?>
</div>