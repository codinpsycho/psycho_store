<script type="text/javascript">
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
<div class="container">	
	<div class="row">
		<div class="col-md-12 text-center top-bottom-space-s">
		<h1> <?php echo $header_title?> </h1>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<h4 class="molot" >Shop <small>By</small>
				<a class= "<?php echo $latest_link_state ?>" href= <?php echo site_url($base_url.'/latest')?> >Latest</a>
				<small> / </small>
				<a href= <?php echo site_url($base_url.'/popular')?> class= "<?php echo $popular_link_state ?>" >Popularity</a>
			</h4>
		</div>	
		<div class="col-md-6">
			<h4 class="molot pull-right">
				<a class="molot" href= <?php echo site_url($prod_1_url) ?> > <?php echo $prod_1_title ?> </a> 
	            <small> / </small>
	            <a class="molot" href= <?php echo site_url($prod_2_url) ?> > <?php echo $prod_2_title ?> </a>
	        </h4>
		</div>
	</div>
	<div class="row well">
		<div class="col-md-12">
			<?php $data['products'] = $products; $this->load->view('catalog',$data);?>
		</div>
	</div>
	<div class="row  top-bottom-space-s">
		<div class="col-md-12">
			<h1><?php echo $seotext_header?></h1>
		</div>
		<div class="col-md-12 well">
			<p><?php echo $seotext_content?></p>
		</div>
	</div>

	

</div>