<div class="row top-bottom-space">
	<div class="col-md-12">
		<h3>#<?php echo $tag_name ?></h3>
		<h5>Show off your geeky side. Tag your loot #<?php echo $tag_name ?> on instagram to get featured here.</h5>
		<hr>
		<div id="instafeed">

			<!-- slider attached on 22.04.2021 -->
			<div class="productslider">
				<div class="owl-carousel owl-theme">

					<?php

					if(isset($product_galleries)) {

						foreach ($product_galleries as $key => $value) 
						{

							?>
							<div class="item">
								<img src="<?=$value['imgurl']?>" class="img-responsive" alt="Product">
							</div>

							<?php

						}
					}

					?>
				</div>
			</div>
			<!-- slider attached on 22.04.2021 -->

		</div>
	</div>
</div>

<!-- Owl Stylesheets -->
<link rel="stylesheet" href="<?=base_url();?>scripts/owlcarousel/assets/owl.carousel.min.css">
<link rel="stylesheet" href="<?=base_url();?>scripts/owlcarousel/assets/owl.theme.default.min.css">
<!-- javascript -->
<script src="<?=base_url();?>scripts/owlcarousel/owl.carousel.js"></script>
<script>
	$(document).ready(function() {
		$('.owl-carousel').owlCarousel({
			loop: true,
			margin: 10,
			nav:true,
			dots:false,
			responsiveClass: true,
			responsive: {
				0: {
					items: 1,

				},
				600: {
					items: 3,
				},
				1000: {
					items: 4,
				}
			}
		})
	})
</script>