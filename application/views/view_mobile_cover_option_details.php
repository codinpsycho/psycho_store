<script type="text/javascript">
function update_prod_image_on_size_select(size_select)
{	
	var selected = size_select.options[size_select.selectedIndex].text.toLowerCase();
	var img_path = 'http://localhost/psycho_store/images/product/2/' + selected.replace(/ /g, '_') + '.png';	
	update_image( img_path  );
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
     <h5 class=""><a class="" href= <?php echo site_url('shipping_returns')?> >Free shipping + 365 days return</a>
     <a class="pull-right" href='#size_chart' data-toggle='modal' data-target="#size_chart">size chart</a> </h5>
  </div>
</div>