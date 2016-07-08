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
				<a class="molot" data-toggle="tooltip" target="_blank" title="Kingdom of gamers geeks and otaku kinights" data-placement="top" href= 'http://psychostore.in/blog' > Psycho Realm </a> 
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

	<div class="row top-bottom-space">
		<div class="col-md-12">
			<h2>What's Your Geek Quotient?</h2>
			<h5>Consider yourself a true geek?</h5>
			<h5> Our knights from the <a target="_blank" href="http://psychostore.in/blog/">psycho realm</a> have created these epic quizzes, for you to test your geekiness and know your geek quotient.</h5>
		</div>
		<div class="col-md-12">
			<div id="myCarousel" class="carousel slide" data-ride="carousel" data-interval="false">
			  <!-- Wrapper for slides -->
				<div class="carousel-inner" role="listbox">
				    <div class="item active">
				    	<iframe src="//renderer.qmerce.com/interaction/5757443b245d1126130297fe"  width="100%" height="491" frameborder="0" scrolling="no"></iframe>
				    	<div class="carousel-caption">
				    	</div>
			    	</div>
				    <div class="item">
				    	<iframe src="//renderer.qmerce.com/interaction/5757dc50245d112613078acd"  width="100%" height="590" frameborder="0" scrolling="no"></iframe>
				    	<div class="carousel-caption">
				      	</div>
				    </div>
				    <div class="item">
						<iframe src="//renderer.qmerce.com/interaction/5778f405eaef76d524362e4d"  width="100%" height="491" frameborder="0" scrolling="no"></iframe>
				      	<div class="carousel-caption">
				      	</div>
				    </div>
			  	</div>

				<!-- Left and right controls -->
				<a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
					<span class="glyphicon glyphicon-chevron-left" aria-hidden=""></span>
					<span class="sr-only">Previous</span>
				</a>
				<a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
					<span class="glyphicon glyphicon-chevron-right" aria-hidden=""></span>
					<span class="sr-only">Next</span>
				</a>
			</div>
		</div>
		<div class="col-md-12 text-center">
			<div class="row">
				<div class="col-md-12">
					<a target="_blank" href="http://psychostore.in/blog" class="btn btn-primary" type="submit">More At Psycho Realm</a>
				</div>
			</div>			
		</div>
	</div>

	<div class="row top-bottom-space">
		<div class="col-md-12">
		<h2 class="molot">Any Comments?</h2>
		<h5>We would love to hear what you think about us or drop us some <a href= <?php echo site_url('auth/saysomething')?> >feedback</a>.</h5>
		<hr>
			<?php $this->load->view('disqus_script')?>
		</div>
	</div>	
</div>