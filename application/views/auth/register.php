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
					<?php echo $this->load->view('view_username') ?>	
				</div>
				<div class="form-group">
				    <?php echo $this->load->view('view_email.php') ?>
				</div>
				<div class="form-group">
					<?php echo $this->load->view('view_password.php') ?>
				</div>	
			</div>
<!-- 			<div class="col-md-1 col-xs-12 vcenter">
		        <h1 class=" text-center play"><small>or</small></h1>
		    </div>
		    <div class="col-md-4 col-xs-12 vcenter">
	          <div class="text-center">
	             <?php echo $this->load->view('google_signin.html')?>
	          </div>
	          <hr>
	          <div class="text-center">
	              <?php echo $this->load->view('fb_login.html')?>
	          </div>
			</div> -->
		</div>
	</div>
	<button class="btn btn-primary" type="submit">Register</button>
	<?php echo form_close(); ?>
</div>