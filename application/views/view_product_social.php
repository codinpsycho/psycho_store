<div class="well">
	<div class="row">
		<div class="col-md-3 col-xs-3">
			<?php $src = "http://www.facebook.com/plugins/like.php?href=$url&layout=button_count&action=like&show_faces=false&share=false&height=21&appId=601282446622582" ?>
			<iframe src="<?php echo $src ?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:21px; width:135px;" allowTransparency="true">
		    </iframe>
	    </div>
		<div class="col-md-3 col-xs-3">
			<a class="twitter-share-button" href=<?php echo $url?> data-size="medium"></a>
		</div>
		<div class="col-md-3 col-xs-3">
			<a href="//www.pinterest.com/pin/create/button/" data-pin-config='beside' data-pin-href=<?php echo $url?> data-pin-do="buttonBookmark" data-count="true"><img src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_gray_20.png" /></a>
		</div>
		<div class="col-md-3 col-xs-3">
			<div class="g-plusone" data-size="medium" data-href=<?php echo $url?> ></div>
		</div>
	</div>	
</div>
