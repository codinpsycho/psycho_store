<?php echo $this->load->view('view_product_modals', null, True); ?>

<script type="text/javascript">
  fbq('track', "ViewContent");  
</script>

<script type="text/javascript">

function update_btn_text_on_addtocart(cb)
{
  var btn = document.getElementById('add_to_cart');
  var selected = "Nothing";
  var size_select = document.getElementById('size_selection');
  
  if(size_select != null)
  {
    var selected = size_select.options[size_select.selectedIndex].text;
  }

  var prod_state = "<?php echo $product_state ?>";

  if(cb.checked)
  {
    var path = "<?php echo site_url("cart/add/{$product['product_id']}")?>";
    $('#anchorcart').text("Add To Cart");
  }
  else
  {
    var path = "<?php echo site_url("cart/instant_checkout/{$product['product_id']}")?>";
    $('#anchorcart').text("Order Now");
  }

  var form = document.getElementById('cart_form');
  form.setAttribute("action", path);

  if(selected.indexOf('Pre-Order') == -1 && prod_state != "preorder")
  {
    btn.innerHTML = cb.checked == true ? "Add To Cart" : "Order Now";            
  }
  else
  {
    btn.innerHTML = cb.checked == true ? "Pre-Order" : "Pre-Order Now";
  }
}
</script>

<div class="container">
    <div class="row top-bottom-space">
      <div class="col-md-12">
        <h1 id="product_name" class="text-left"><?php echo $product['product_name'] ?>
        <span class="pull-right"> <i class="fa fa-rupee"></i> <?php echo $product['product_price']?> </span> </h1>
        <hr>
      </div>
      <div class="col-md-12">
        <ul class="pager">
          <li class="previous">
            <?php echo anchor("$prev_id", "Previous");?>
          <li class="next">
            <?php echo anchor("$next_id", "Next");?>
          </li>
        </ul>
      </div>     
      <div class="col-md-6 text-center">
        <?php echo $product_img_view; ?>
        <?php // echo $this->load->view('view_product_social', null, True); ?>
      </div>
      <div class="col-md-4">
        <div class="row">
          <div class="col-md-12">
            <p> <?php echo $product['product_intro']; ?></p>
            <a href="#prod_desc"><i class="fa fa-caret-down"></i> read more</a>
            <hr>
          </div>          
        </div>
        <?php echo $details_view?>        
        <hr>        
        <div class="row ">
          <div class="col-md-12">
            <h4 class="molot"><a href=<?php $game = url_title($product['product_game']); echo site_url("like/$game")?>> More from <?php echo $product['product_game']?></a>
            <hr>
          </div>
          <div class="col-md-12">
            <?php echo $this->load->view('view_product_type', null, True); ?>
          </div>
        </div>
      </div>
    </div>

  <!-- switched galleries with suggestions -->
  <?php 
  $data['tag_name'] = $hashtag; 
  $data['product_galleries'] = $product_galleries;
  if(count($product_galleries) > 0)
  echo $this->load->view('view_product_instagram', $data, True); 
  ?>

  
  <div id='prod_desc'>
    <?php echo $this->load->view('view_product_desc', null, True); ?>
  </div>

  <!-- closed on 31.05.2021 -->
  <?php // $data['product_galleries'] = $product_galleries; echo $this->load->view('product_galleries', $data, True); ?>

  <!-- switched suggestions with galleries -->
  <?php $data['suggested_products'] = $suggested_products; echo $this->load->view('product_suggestions', $data, True); ?>
  
  <?php $data['product_name'] = $product['product_name']; echo $this->load->view('view_disqus', $data, True); ?>

  <?php echo $this->load->view('view_product_recent', null, True); ?>
</div>