<link rel="stylesheet" href=<?php echo site_url('css/bootstrap-social.css') ?> >

<div class="container top-bottom-space ">
	<h1>Register</h1>
	<hr>
	<div class="well">
		<div class="row">
			<div class="col-md-6 col-xs-12 vcenter">
				<?php $redirect_url =  rawurlencode($this->input->get('redirect_url'))?>
        		<?php $attributes = array('id' => 'login_form');?>
				<?php echo form_open($this->uri->uri_string().'?redirect_url='.$redirect_url, $attributes); ?>
				<div class="form-group">
					<?php echo $this->load->view('view_username', null, True) ?>	
				</div>
				<div class="form-group">
				    <?php echo $this->load->view('view_email.php', null, True) ?>
				</div>
				<div class="form-group">
					<?php echo $this->load->view('view_password.php', null, True) ?>
				</div>	
			</div>
		</div>
	</div>
	<button class="btn btn-primary" type="submit">Register</button>
	<?php echo form_close(); ?>
</div>