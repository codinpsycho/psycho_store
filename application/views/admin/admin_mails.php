<div class="container top-bottom-space">  
    <h1>Emails        
       <span class="pull-right navbar-text"> <small><?php echo $num_subscribers?> subscribers </small></span>
    </h1>
    <hr>
    <div class="well">
        <div class="row">
            <div class="col-md-12">
                <form class='form-inline' method="post" action= <?php echo site_url('admin/mails') ?> >
                     <div class="form-group">
                        <input class="form-control" type="text" placeholder="you@email.com" name="subscriber_email">
                        <button type="submit" class="btn btn-default">add subscriber</button>
                        <a href=<?php echo site_url('admin/send_mail')?> class="btn  btn-default">send custom mail</a>
                      </div>
                </form>
            </div>
        </div>        
    </div>
    <div class="well">
    	<div class="row">
	    	<div class="col-md-12">
	    		<form class='form-inline' method="post" action= <?php echo site_url('admin/mails') ?> >
                    <div class="form-group">
                        <?php echo $this->load->view('view_email', null, True) ?>
                        <label>Type </label>
                        <select class="form-control" name="mail_type">
                            <option value="activate"> Activate </option>
                            <option value="subscribe"> Subscribe </option>
                            <option value="order"> Order </option>
                            <option value="first_order"> First Order </option>
                        </select>
                        <button type="submit" class="btn btn-primary">Send mail</button>
                    </div>
                </form>
			</div>
            <div class="col-md-12">
                <p>Newsletter</p>
                <form class='form-inline' method="post" action= <?php echo site_url('admin/test_mass_mail') ?> >
                    <div class="form-group">
                        <input type="text" class="form-control" name="subject" placeholder="Subject">
                    </div>
                    <button type="submit" class="btn btn-primary">Test Newsletter</button>                    
                </form>
                <form class='form-inline' method="post" action= <?php echo site_url('admin/mass_mail') ?> >
                    <div class="form-group">
                        <input type="text" class="form-control" name="subject" placeholder="Subject">
                    </div>
                    <button disabled type="submit" class="btn btn-primary">Send Newsletter</button>
                </form>
            </div>            
            <div class="col-md-12">
                <h3>Latest Subscribers</h3>
                <?php foreach ($latest_subscribers as $key => $subscriber): ?>
                    <p> <?php echo $subscriber['email']?></p>
                <?php endforeach; ?>
            </div>
		</div>        
	</div>
</div>
