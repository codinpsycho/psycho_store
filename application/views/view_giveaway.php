<link rel="stylesheet" href=<?php echo site_url('css/bootstrap-social.css') ?> >
<div class="container top-bottom-space">
    <h1>Want free merchandises? </h1>
    <hr> 
    <div class="well">
    	<div class="row ">
	    	<div class="col-md-12">
	    		<p>Our minions crawl various social media and keep track of good fellows from earth who share about us.
	    		On 15th and 30th of each month they come back to us with their results and we chose one random winner from the list who gets free psychostore merchandise.
			</div>
		</div>		
		<hr>
		<div class="row ">
			<div class="col-md-4 text-center">
				<h4 class="molot ">Step 1</h4>
				<h5 class=""> connect with us</h5>
				<?php $redirect_url = rawurlencode(uri_string()); 
				$final_url = 'auth/login?redirect_url='.$redirect_url; ?>
		        <form id="login_form" method = "post" action = <?php echo $final_url ?> >
				<div class="top-bottom-space">
					<?php $this->load->view('google_signin.html')?>
					<h5>or</h5>
					<?php $this->load->view('fb_login.html')?>
				</div>
				</form>
			</div>
			<div class="col-md-4 text-center">
				<h4 class="molot ">Step 2</h4>
				<h5 class=""> Follow us</h5>
				<p>Follow us on social media to stay updated with latest releases and offers.</p>
                    <iframe src="https://www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Fpsychostorein&layout=button_count&action=like&show_faces=false&share=true&height=21&appId=601282446622582" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:21px; width:135px;" allowTransparency="true">
                    </iframe>
				<a href="https://twitter.com/psychostorein" class="twitter-follow-button" data-show-count="false" data-lang="en" show-screen-name="false" data-size="small"></a>
				<style>.ig-b- { display: inline-block; }
                    .ig-b- img { visibility: hidden; }
                    .ig-b-:hover { background-position: 0 -60px; } .ig-b-:active { background-position: 0 -120px; }
                    .ig-b-v-24 { width: 137px; height: 24px; background: url(//badges.instagram.com/static/images/ig-badge-view-sprite-24.png) no-repeat 0 0; }
                    @media only screen and (-webkit-min-device-pixel-ratio: 2), only screen and (min--moz-device-pixel-ratio: 2), only screen and (-o-min-device-pixel-ratio: 2 / 1), only screen and (min-device-pixel-ratio: 2), only screen and (min-resolution: 192dpi), only screen and (min-resolution: 2dppx) {
                    .ig-b-v-24 { background-image: url(//badges.instagram.com/static/images/ig-badge-view-sprite-24@2x.png); background-size: 160px 178px; } }</style>
                    <a href="http://instagram.com/psychostore.in?ref=badge" class="ig-b- ig-b-v-24"><img src="//badges.instagram.com/static/images/ig-badge-view-24.png" alt="Instagram" /></a>
			</div>
			<div class="col-md-4 text-center">
				<h4 class="molot ">Step 3</h4>
				<h5 class=""> Share</h5>
				<p>Share the merchandise on social media that you want to win and let us know why? Make up something interesting and don't forget to tag us.</p>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 text-center">
				<h2>And you are done!</h2>
				<h5>Just wait for us to announce the winners on 15th and 30th of every month.</h5>
			</div>
		</div>
	</div>
</div>
