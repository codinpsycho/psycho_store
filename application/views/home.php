<script type="text/javascript">
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
<div class="container">	
	<div class="row">
		<div class="col-md-12 text-center top-bottom-space-s">
		<img src=<?php echo site_url("images/logo.png") ?>>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<h4 class="molot" >Shop&nbsp;<small>By</small> <a class = <?php echo $latest_link_state ?> href="latest">Latest</a><small>&nbsp;/&nbsp;</small><a class = <?php echo $popular_link_state ?> href="popular">Popularity</a>
		</div>
		<div class="col-md-6">
		<h4>
			<span class="pull-right">
				<a class="molot" data-toggle="tooltip" title="win free stuff" data-placement="top" href= <?php echo site_url('giveaways')?> > Giveaways </a> 
	            	<small>/</small>
	            	<a class="molot" data-toggle="tooltip" title="see what people are saying about us" data-placement="top" href= <?php echo site_url('feedback')?> > Reviews </a>
	            	<small>/</small>
	            	<a class="molot" data-toggle="tooltip" title="gaming in india" data-placement="right" href= <?php echo site_url('insights')?> > Statistics </a>
            </span>
        </h4>
		</div>
	</div>
	<div class="row well">
		<div class="col-md-12">
			<?php $data['products'] = $products; $this->load->view('catalog',$data);?>
		</div>
	</div>

	<?php $data['tag_name'] = 'psychofamous'; echo $this->load->view('view_product_instagram', $data); ?>
	<div class="row">
		<div class="col-md-12">
			<a class ="btn btn-default btn-tabbed" href="">View All</a>
		</div>
	</div>

	<!-- <div class="row top-bottom-space">
		<div class="col-md-12">
			<h2>Psycho Lab updates</h2>
			<h5>Get up to date with our scientists at Psycho Labs, as they blog about their experience.</h5>
			<hr>
		</div>
		<div class="col-md-4">
			<div class="tumblr-post" data-href="https://embed.tumblr.com/embed/post/ydtRnGIEjsJLy5PyTsCdcA/139231574131" data-did="0f33af7f0c3b71e24f567863b76525403a751edd"><a href="http://psychostorein.tumblr.com/post/139231574131/understanding-the-concept-of-being-remarkable">http://psychostorein.tumblr.com/post/139231574131/understanding-the-concept-of-being-remarkable</a></div><script async src="https://secure.assets.tumblr.com/post.js"></script>
		</div>
		<div class="col-md-4">
			<div class="tumblr-post" data-href="https://embed.tumblr.com/embed/post/ydtRnGIEjsJLy5PyTsCdcA/138974414701" data-did="f5607c02b1df939a364f1ac20812b7e8f24c2859"><a href="http://psychostorein.tumblr.com/post/138974414701/shashikanthreddy-spower-tshirt">http://psychostorein.tumblr.com/post/138974414701/shashikanthreddy-spower-tshirt</a></div><script async src="https://secure.assets.tumblr.com/post.js"></script>
		</div>
		<div class="col-md-4">
			<div class="tumblr-post" data-href="https://embed.tumblr.com/embed/post/ydtRnGIEjsJLy5PyTsCdcA/138610644421" data-did="63733c0e9073ef999b013b9793fbe60a734af88b"><a href="http://psychostorein.tumblr.com/post/138610644421/so-this-happened-at-video-game-fest-16-pune">http://psychostorein.tumblr.com/post/138610644421/so-this-happened-at-video-game-fest-16-pune</a></div><script async src="https://secure.assets.tumblr.com/post.js"></script>
		</div>
	</div> -->

	<div class="row top-bottom-space">
		<div class="col-md-12">
		<h2 class="molot">Any Comments?</h2>
		<h5>We would love to hear what you think about us or drop us some <a href= <?php echo site_url('auth/saysomething')?> >feedback</a>.</h5>
		<hr>
			<?php $this->load->view('disqus_script')?>
		</div>
	</div>	
</div>