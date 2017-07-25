<div class="row">
  <?php 
  foreach($products as $product_item):
    $url = product_url($product_item);    
  	$path = "/".$product_item['product_image_path'];
    $img_alt = $product_item['product_intro'];
  	$image_properties = array(
            'src' => "$path",          
            'class' => 'img-responsive',
            'alt' => "$img_alt",
  );
  ?>
  	<div class="col-md-4 col-xs-6 ">
      <div class="product-link">
      	<?php echo anchor($url, img($image_properties));?>
      	<div class="row">
  	    	<div class="col-md-12 catalog-desc">
  	    		<p class="text-center"> <?php echo $product_item['product_name'] ?>
             <h5 class="text-center"> <i class="fa fa-rupee"></i> <?php echo $product_item['product_price'] ?></h5></p>
  	    	</div>
      	</div>    
      </div>
  </div>
  <?php endforeach ?>
</div>