<div class="row top-bottom-space">
	<div class="col-md-12">
	    <h5>Lets Discuss '<?php echo $product_name?>' here or you can send us <a href= <?php echo site_url('auth/saysomething')?> >feedback</a>
	    </h5>
	    <hr>
	    <?php $this->view('disqus_script')?>
	</div>    
</div>