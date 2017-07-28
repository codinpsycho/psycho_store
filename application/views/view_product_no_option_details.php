<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">    
    <div class="checkbox">
      <label class="pull-right"><input id="add_to_cart_checkbox" onclick="update_btn_text_on_addtocart(this)" type="checkbox" name="optradio">add to cart</label>
    </div>
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12">
    <form id="cart_form" method = "post" action = <?php echo site_url("cart/instant_checkout/{$product['product_id']}")?> role="form">
    </div>
    <div class="col-md-12 col-sm-12 col-xs-12">
      <?php $button_text = 'Order Now'?>
      <button type="submit" name = "add_to_cart" id="add_to_cart" class="btn btn-primary btn-block"><?php echo $button_text?></button>
    </div>
  </form> 
  <div class="col-md-12 col-sm-12 col-xs-12">
     <h5 class=""><a class="" href= <?php echo site_url('shipping_returns')?> >free shipping + 365 days return</a>
  </div>
</div>