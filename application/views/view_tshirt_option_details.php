<script type="text/javascript">
  function update_btn_text_on_size_select(size_select)
  {
    <?php if($show_size_preorder_info): ?>

      var btn = document.getElementById('add_to_cart');
      var selected = size_select.options[size_select.selectedIndex].text;
      var cb = document.getElementById('add_to_cart_checkbox'); 

      if(selected.indexOf('Pre-Order') == -1)
      {
        btn.innerHTML = cb.checked == true ? "Add To Cart" : "Order Now";
      }
      else
      {
        btn.innerHTML = cb.checked == true ? "Pre-Order" : "Pre-Order Now";
      }

    <?php endif; ?>  
  }

</script>

<script>
  $(window).scroll(function() {
    if ($(this).scrollTop() > 150) {
      $('.stky').addClass('sticky');
    } else {
      $('.stky').removeClass('sticky');
    }
  });
</script>

<div class="w-100">
  <div class="row">

    <!-- showing alert msg to select a size -->
    <div class="col-md-7 col-sm-12 col-xs-12" id="ordersize">
      <span id="orderalert" style="display: none;">Please select a size</span>
    </div>


    <div class="col-md-5 col-sm-12 col-xs-12">
      <?php if($product_state == 'preorder'): ?>
        <h5 class="pull-left"><a class="" href='#preorder' data-toggle='modal' data-target="#preorder">Why Pre-order? (Ships on <?php echo $restock_date ?>)</a> </h5>
      <?php endif; ?>
      <?php if($show_size_preorder_info): ?>
        <h5 class="pull-left"><a class="" href='#size_preorder' data-toggle='modal' data-target="#size_preorder">Pre-Orders shipping from <?php echo $restock_date ?></a> </h5>
      <?php endif; ?> 

      <div class="checkbox mobcheck">
        <label class="pull-right "><input id="add_to_cart_checkbox" onclick="update_btn_text_on_addtocart(this)" type="checkbox" name="optradio">add to cart</label>
      </div>
    </div>
    <div class="col-md-6 col-sm-12 col-xs-12">
      <form id="cart_form" method = "post" action = "<?php echo site_url("cart/instant_checkout/{$product['product_id']}")?>" role="form">

        <select id="size_selection" required class="form-control" name="extra" onchange="update_btn_text_on_size_select(this)">

          <option disabled selected value="">Select Size</option>

          <option <?php echo $small_stock; ?> value ="Small">Small 
            <?php if($small_stock == 'disabled') echo '(Out Of Stock)';
            elseif ($small_stock == 'preorder') echo '(Pre-Order)';?>
          </option>

          <option <?php echo $medium_stock; ?> value ="Medium">Medium
            <?php if($medium_stock == 'disabled') echo '(Out Of Stock)';
            elseif ($medium_stock == 'preorder') echo '(Pre-Order)';?>
          </option>

          <option <?php echo $large_stock; ?> value ="Large">Large
            <?php if($large_stock == 'disabled') echo '(Out Of Stock)';
            elseif ($large_stock == 'preorder') echo '(Pre-Order)';?>
          </option>

          <option <?php echo $xl_stock; ?> value ="XL">XL
            <?php if($xl_stock == 'disabled') echo '(Out Of Stock)';
            elseif ($xl_stock == 'preorder') echo '(Pre-Order)';?>            
          </option>

          <!-- dev on 06.07.2021 -->
          <option <?php echo $xxl_stock; ?> value ="XXL">XXL
            <?php if($xxl_stock == 'disabled') echo '(Out Of Stock)';
            elseif ($xxl_stock == 'preorder') echo '(Pre-Order)';?>
          </option>

        </select>


      </div>
      <div class="w-100 stky">
        <div class="col-md-6 col-sm-12 col-xs-12">
          <?php $button_text = $product_state == 'preorder' ? 'Pre-Order Now' : 'Order Now'?>

          <button type="submit" name="add_to_cart" id="add_to_cart" class="btn btn-primary btn-block" style="display: none;"><?php echo $button_text?></button>

          <a href="#ordersize" role="button" id="anchorcart" class="btn btn-primary btn-block"><?php echo $button_text?></a>

        </div>
      </div>
    </form> 
    <div class="col-md-12 col-sm-12 col-xs-12">
      <h5 class=""><a class="" href= <?php echo site_url('shipping_returns')?> >free shipping + 365 days return</a>
       <a class="pull-right" href='#size_chart' data-toggle='modal' data-target="#size_chart">size chart</a> </h5>
     </div>
   </div>
 </div>


 <script type="text/javascript">

  $('#anchorcart').click(function (e) {
    $('#orderalert').show();
  });

  $('#size_selection').change(function (e) {

   $('#anchorcart').hide();
   $('#add_to_cart').show();
   $('#orderalert').hide();

 });
</script>