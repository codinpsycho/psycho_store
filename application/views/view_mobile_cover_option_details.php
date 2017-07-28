<script type="text/javascript">
//Helper Functions
function load_image_async(url)
{
  request = new XMLHttpRequest();
  request.open('GET', url, true);
  request.onload = function()
  {
    update_image(url);
  };

  request.onprogress = function()
  {
  	//gif
  	var path_prefix = "<?php echo site_url() ?>"
	var img_path = path_prefix + '/images/ellipses.svg';
    update_image(img_path);
  };
  
  request.send();
}

function update_prod_image_on_size_select(size_select)
{	
	var selected = size_select.options[size_select.selectedIndex].text.toLowerCase();
	var path_prefix = "<?php echo site_url() ?>"
	var img_path = path_prefix + '/images/product/<?php echo $product['product_id'] ?>/models/' + selected.replace(/ /g, '_') + '.png';	
	load_image_async( img_path );
}

function update_image(path)
{
	prod_img = document.getElementById('prod_img');
	if(prod_img)
	{
	  prod_img.setAttribute("src", path);
	}
}


</script>
<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">    
    <div class="checkbox">
      <label class="pull-right "><input id="add_to_cart_checkbox" onclick="update_btn_text_on_addtocart(this)" type="checkbox" name="optradio">add to cart</label>
    </div>
  </div>
  <div class="col-md-12 col-sm-12 col-xs-12">
    <form id="cart_form" method = "post" action = <?php echo site_url("cart/instant_checkout/{$product['product_id']}")?> role="form">
      <select id="size_selection" required class="form-control" name="extra" onchange="update_prod_image_on_size_select(this)">
        <option disabled selected value="">Select Your Model</option>
        <?php foreach($supported_models as $key => $model): ?>
        	<option value = <?php echo urlencode($model['model_name']) ?> > <?php echo $model['model_name'] ?> </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-12 col-sm-12 col-xs-12 top-bottom-space-xs">      
      <button type="submit" name = "add_to_cart" id="add_to_cart" class="btn btn-primary btn-block">Order Now</button>
    </div>
  </form> 
  <div class="col-md-12 col-sm-12 col-xs-12">
     <h5 class=""><a class="" href= <?php echo site_url('shipping_returns')?> >free shipping + 365 days return</a>     
  </div>
</div>