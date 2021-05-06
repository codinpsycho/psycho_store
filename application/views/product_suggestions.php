<div class="row">
  <div class="col-md-12">
    <h3 class="molot">Our algorithm also suggests </h3>
    <hr>
  </div>
  <div class="col-md-12">
    <?php
    foreach($suggested_products as $key => $product_item):
      $prod_url = product_url($product_item);
      $path = "/".$product_item['product_image_path'];
      $image_properties = array(
        'src' => "$path",
        'class' => 'img-responsive',
      );
      ?>

      <div class="product-link-sm col-md-2 col-sm-4 col-xs-4">
        <a href= <?php echo site_url($prod_url)?> >
          <?php echo img($image_properties);?>
          <p  class="text-center"><?php echo $product_item['product_name']?></p>
        </a>
      </div>

      <?php
      if($key == 4)
      {
        break;
      }
      ?>
    <?php endforeach ?>            
  </div>
</div>