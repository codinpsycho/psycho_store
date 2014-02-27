<div class="container">
  <div>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-3">
        <h4 class="lead">Sort&nbsp;<small>By</small> <a class ="active" href="latest">Latest</a><small>&nbsp;/&nbsp;</small><a href="popular">Popularity</a></h4> 
      </div>
    </div>
  </div>
</div>

<div class="container">
  <div class="well">
    <div class="row">
<?php 
foreach($products as $product_item): 
	$id = $product_item['product_id'];
	$path = "/".$product_item['product_image_path'];
	$image_properties = array(
          'src' => "$path",          
          'class' => 'img-responsive',
);
?>
	<div class="col-md-4">
    	<?php echo anchor("/product/$id", img($image_properties));?>
    	<div class="row">
	    	<div class="col-md-12">
	    		<h4 class="text-center"> <?php echo anchor("product/$id", $product_item['product_name']) ?> <br>Rs <?php echo $product_item['product_price'] ?></h4>
	    	</div>
    	</div>
    </div>

<?php endforeach ?>


      
    </div>
  </div>
</div>
