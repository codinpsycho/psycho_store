<div class="row">

	<div class="col-md-12">
      	<?php
            foreach($other_prod_types as $prod):
              $prod_url = product_url($prod); 
              $path = "/".$prod['product_image_path'];
              $image_properties = array(
                      'src' => "$path",
                      'class' => 'img-responsive',
            );
            ?>
		<div class="product-link-sm col-md-4 col-sm-4 col-xs-4">
        	<?php echo anchor($prod_url, img($image_properties));?>
        </div>
    	<?php endforeach  ?>
	</div>
</div>