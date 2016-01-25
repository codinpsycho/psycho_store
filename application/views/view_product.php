<div class="modal fade" id="size_chart" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <img class='img-responsive' src= <?php echo $size_chart ?> >
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default text-center" data-dismiss="modal">Got it!</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="preorder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="molot modal-title text-center" id="modal_title"> Grab this loot before it vanishes </h4>
      </div>
      <div class="modal-body">
        <h5>Ques : Why should i pre-order?<br><br>
        Ans : Look at the loot, just look at it damn it. You know how many mercenaries have been hired to snatch this loot from us and you ask why should you pre-order. Our production minions are playing with their lives here to get you this loot and for that we need your confirmation as there is a limit to everything.<br><br>So pre-order this right now and we start shipping from <strong>2nd November</strong>.
        </h5>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default text-center" data-dismiss="modal">Hell Yeah! I Want One.</button>
      </div>
    </div>
  </div>
</div>

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
          <div class="col-md-12">
            <h5><a class="inline" href='#size_chart' data-toggle='modal' data-target="#size_chart">size chart</a> 
            <?php if($product_state == 'preorder'): ?>
              <span class="pull-right"><a class="inline" href='#preorder' data-toggle='modal' data-target="#preorder">Why Pre-order?</a> </span>
            <?php endif; ?>  
            </h5>
          </div>
          <div class="col-md-4">
            <form  method = "post" action = <?php echo site_url("cart/add/{$product['product_id']}")?> role="form">
              <select class="form-control" name="size">
                  <option <?php echo $small_stock; ?> value ="Small">Small <?php if($small_stock == 'disabled') echo '(Out Of Stock)';?> </option>
                  <option <?php echo $medium_stock; ?> value ="Medium">Medium <?php if($medium_stock == 'disabled') echo '(Out Of Stock)';?> </option>
                  <option <?php echo $large_stock; ?> value ="Large">Large <?php if($large_stock == 'disabled') echo '(Out Of Stock)';?> </option>
                  <option <?php echo $xl_stock; ?> value ="XL">XL <?php if($xl_stock == 'disabled') echo '(Out Of Stock)';?> </option>
              </select>
            </div>  
            <div class="col-md-8">
              <?php $button_text = $product_state == 'preorder' ? 'Pre-Order' : 'Add To Cart'?>
              <button type="submit" name = "add_to_cart" id="add_to_cart" class="btn btn-primary btn-block"><?php echo $button_text?></button>
            </div>
          </form> 
          <div class="col-md-12">
             <h5 class=""><a class="" href= <?php echo site_url('shipping_returns')?> >Free shipping + 365 days return</a></h5>
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
  
  <?php $data['product_name'] = $product['product_name']; echo $this->load->view('view_disqus', $data); ?>

  <?php echo $this->load->view('view_product_recent'); ?>
</div>