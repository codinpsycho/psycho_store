<script type="text/javascript">
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>


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
         <?php echo form_open($this->uri->uri_string().'?redirect_url='.$redirect_url); ?>
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
          <?php if ($this->config->item('allow_registration', 'tank_auth')) echo anchor('/auth/register/', ' Register'); ?> \
          <?php echo anchor('/auth/forgot_password/', 'Forgot password'); ?>
        </div>      

        <div class="col-md-1 col-xs-12 vcenter">
          <h1 class=" text-center play"><small>or</small></h1>
        </div>
        <div class="col-md-4 col-xs-12 vcenter">
            <a href = <?php echo site_url('auth/register')?>>  <h1 class="text-center">Register</h1> </a>
        </div>
      </div>
    </div>
      <div class="col-md-12">
        <button class="btn btn-primary" data-toggle="tooltip" title="No need to insert coins!" data-placement="right" type="submit">Start the game!</button>
        <?php echo form_close(); ?>
      </div>
  </div>