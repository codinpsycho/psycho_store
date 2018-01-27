<div class="container">  
    <div class="row">
      <div class="col-md-12 top-bottom-space-s">
        <h1 class="text-center"><?php echo $search_text;?> T-Shirts/Merchandise</h1>
        <h5 class="text-center grey-text"><?php echo $search_result ?>&nbsp;product(s) found to satisfy your inner geek!</h5>
      </div>
    </div>  
    <div class="row well">
    	<div class="col-md-12">
    		<?php $data['products'] = $products; $this->load->view('catalog',$data);?>
		</div>
	</div>
</div>


