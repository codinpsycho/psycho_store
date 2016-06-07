<?php echo $this->load->view('view_product_modals', null); ?>

<script type="text/javascript">
  function update_addtocart_btn_text()
  {
    <?php if($show_size_preorder_info): ?>
    
      var btn = document.getElementById('add_to_cart');
      var size_select = document.getElementById('size_selection');
      var selected = size_select.options[size_select.selectedIndex].text;
      if(selected.indexOf('Pre-Order') == -1)
      {
        btn.innerHTML = "Add To Cart";
      }
      else
      {
        btn.innerHTML = "Pre-Order";
      }

    <?php endif; ?>  
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
          </li>
          <li class="next">
            <?php echo anchor("$next_id", "Next");?>
          </li>
        </ul>
      </div>      
      <div class="col-md-6 text-center">
        <?php $data['img_alt'] = $product['product_intro']; echo $this->load->view('view_product_image', $data)?>
        <?php echo $this->load->view('view_product_social', null); ?>
      </div>
      <div class="col-md-4">
        <div class="row">
          <div class="col-md-12">
            <p> <?php echo $product['product_intro']; ?></p>
            <a href="#prod_desc"><i class="fa fa-caret-down"></i> read more</a>
            <hr>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <?php if($product_state == 'preorder'): ?>
              <h5 class="pull-right"><a class="" href='#preorder' data-toggle='modal' data-target="#preorder">Why Pre-order? (Ships on <?php echo $restock_date ?>)</a> </h5>
            <?php endif; ?>
            <?php if($show_size_preorder_info): ?>
              <h5 class="pull-right"><a class="" href='#size_preorder' data-toggle='modal' data-target="#size_preorder">Pre-Orders shipping from <?php echo $restock_date ?></a> </h5>
            <?php endif; ?>            
          </div>
          <div class="col-md-4 col-sm-12 col-xs-12">
            <form  method = "post" action = <?php echo site_url("cart/add/{$product['product_id']}")?> role="form">
              <select id="size_selection" required class="form-control" name="size" onchange="update_addtocart_btn_text()">
                <option disabled selected value="">Select Size</option>  
                <option <?php echo $small_stock; ?> value ="Small">Small 
                <?php if($small_stock == 'disabled') echo '(Out Of Stock)';
                elseif ($small_stock == 'preorder') echo '(Pre-Order)';?>
                </option>
                <option <?php echo $medium_stock; ?> value ="Medium">Medium
                <?php if($medium_stock == 'disabled') echo '(Out Of Stock)';
                elseif ($medium_stock == 'preorder') echo '(Pre-Order)';?></option>
                <option <?php echo $large_stock; ?> value ="Large">Large
                <?php if($large_stock == 'disabled') echo '(Out Of Stock)';
                elseif ($large_stock == 'preorder') echo '(Pre-Order)';?></option>
                <option <?php echo $xl_stock; ?> value ="XL">XL
                <?php if($xl_stock == 'disabled') echo '(Out Of Stock)';
                elseif ($xl_stock == 'preorder') echo '(Pre-Order)';?></option>
              </select>
            </div>
            <div class="col-md-8 col-sm-12 col-xs-12">
              <?php $button_text = $product_state == 'preorder' ? 'Pre-Order' : 'Add To Cart'?>
              <button type="submit" name = "add_to_cart" id="add_to_cart" class="btn btn-primary btn-block"><?php echo $button_text?></button>
            </div>
          </form> 
          <div class="col-md-12 col-sm-12 col-xs-12">
             <h5 class=""><a class="" href= <?php echo site_url('shipping_returns')?> >Free shipping + 365 days return</a>
             <a class="pull-right" href='#size_chart' data-toggle='modal' data-target="#size_chart">size chart</a> </h5>
          </div>
        </div>
        <hr>
        <div class="row ">
          <div class="col-md-12">
            <h5>Inspired by&nbsp;<span class="h4 molot"><a href=<?php $game = url_title($product['product_game']); echo site_url("like/$game")?>> <?php echo $product['product_game']?></a> </span></h5>
          </div>
          <div class="col-md-12">
            <hr>
            <?php
            foreach($suggested_products as $product_item):
              $prod_url = product_url($product_item); 
              $path = "/".$product_item['product_image_path'];
              $image_properties = array(
                      'src' => "$path",
                      'class' => 'img-responsive',
            );
            ?>
              <div class="product-link-sm col-md-4 col-sm-4 col-xs-4">
                  <?php echo anchor($prod_url, img($image_properties));?>      
              </div>

            <?php endforeach ?>
          </div>
        </div>
      </div>
    </div>  

  <div id='prod_desc'>
    <?php echo $this->load->view('view_product_desc'); ?>
  </div>

  <?php $data['tag_name'] = $hashtag; echo $this->load->view('view_product_instagram', $data); ?>
  
  <?php $data['product_name'] = $product['product_name']; echo $this->load->view('view_disqus', $data); ?>

  <?php echo $this->load->view('view_product_recent'); ?>
</div>