<script type="text/javascript">
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
<link rel="stylesheet" href=<?php echo site_url('css/bootstrap-social.css') ?> >

<div class="container top-bottom-space ">
	<h1>Login</h1>
	<hr>
  <div class="well">
  <div class="row">
    <div class="col-md-6 col-xs-12 col-s-12 vcenter">      
        <?php
        $remember = array(
          'name'  => 'remember',
          'id'  => 'remember',
          'value' => 1,
          'checked' => set_value('remember'),
          'style' => 'margin:0;padding:0',
          'type'  => 'checkbox',
          'class' =>  'checkbox'
        );

        ?>
        <?php $redirect_url =  rawurlencode($this->input->get('redirect_url'))?>
        <?php $attributes = array('id' => 'login_form');?>
         <?php echo form_open($this->uri->uri_string().'?redirect_url='.$redirect_url, $attributes); ?>
         <div class="form-group">
            <?php echo $this->load->view('view_email') ?>
          </div>
          <div class="form-group">
            <?php echo $this->load->view('view_password') ?>
          </div>
          <div class="checkbox">
            <label>
              <input type="checkbox"> Remember Me
            </label>
          </div>
          <?php if ($this->config->item('allow_registration', 'tank_auth')) echo anchor("/auth/register/?redirect_url=$redirect_url", ' Register'); ?> \
          <?php echo anchor('/auth/forgot_password/', 'Forgot password'); ?>
        </div>
        <div class="col-md-1 col-xs-12 vcenter">
          <h1 class=" text-center play"><small>or</small></h1>
        </div>
        <div class="col-md-4 col-xs-12 vcenter">
          <div class="text-center">
             <?php echo $this->load->view('google_signin.html', array("gauth_url" => $gauth_url ) ) ?>
          </div>
          <hr>
          <div class="text-center">
             <?php echo $this->load->view('fb_login.html')?>
          </div>          
        </div>
      </div>
    </div>
      <div class="col-md-12">
        <button class="btn btn-primary" data-toggle="tooltip" title="No need to insert coins!" data-placement="right" type="submit">Start the game!</button>
        <?php echo form_close(); ?>
      </div>
  </div>