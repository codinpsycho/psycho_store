<div class="row">
  <div class="col-md-12">
      <h3 class="molot">Our algorithm also suggests <small> (randomly)</small></h3>
      <hr>
  </div>
  <div class="col-md-12">
    <?php
    foreach($suggested_products as $product_item):
      $prod_url = product_url($product_item); 
      $path = "/".$product_item['product_image_path'];
      $image_properties = array(
              'src' => "$path",
              'class' => 'img-responsive',
    );
    ?>
      <div class="product-link-sm col-md-2 col-sm-4 col-xs-4">
          <?php echo anchor($prod_url, img($image_properties));?>
      </div>

    <?php endforeach ?>            
  </div>
</div>