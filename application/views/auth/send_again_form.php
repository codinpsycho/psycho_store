<div class="container top-bottom-space">
    <h1>Send Activation Mail </h1>
    <hr> 
    <div class="well">
    	<div class="row ">
	    	<div class="col-md-12">
	    	 	<?php echo form_open($this->uri->uri_string()); ?>
	    		<div class="form-group">
					<?php echo $this->load->view('view_email', null, True) ?>
				</div>
			</div>
		</div>	
	</div>
	<button class="btn btn-primary" type="submit">Send Again</button>
	<?php echo form_close(); ?>
</div>
