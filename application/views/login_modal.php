
<link rel="stylesheet" href=<?php echo site_url('css/bootstrap-social.css') ?> >

<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="loginModalLabel">Login</h5>
      </div>
      

      <?php $redirect_url =  rawurlencode($this->input->get('redirect_url'))?>
      <?php $attributes = array('id' => 'login_form');?>
      <?php 
      echo form_open(base_url().'auth/login?redirect_url='.$redirect_url, $attributes); 
      ?>

      <!-- get the current page url -->
      <input type="hidden" name="page" value="<?=current_url();?>">

      <div class="modal-body">

        <div class="row">
          <div class="col-md-6 col-xs-12 col-s-12 vcenter">
            <span style="color: red"><?php echo $this->session->flashdata('error'); ?></span>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 col-xs-12 col-s-12 vcenter">
            <div class="form-group">
              <?php echo $this->load->view('view_email', null, True) ?>
            </div>
            <div class="form-group">
              <?php echo $this->load->view('view_password', null, True) ?>
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox"> Remember Me
              </label>
            </div>

            <?php if ($this->config->item('allow_registration', 'tank_auth')) echo anchor("/auth/register/?redirect_url=$redirect_url", ' Register'); ?> \
            <?php echo anchor('/auth/forgot_password/', 'Forgot password'); ?>
          </div>

          <div class="col-md-1 col-xs-12 col-s-12 vcenter">
            <h1 class=" text-center play"><small>or</small></h1>
          </div>
          <div class="col-md-4 col-xs-12 col-s-12 vcenter">
            <div class="text-center">
              <?php echo $this->load->view('google_signin.html', array("gauth_url" => $gauth_url ), True ) ?>
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Start the game!</button>
      </div>
      <?php echo form_close(); ?>
      
    </div>
  </div>
</div>