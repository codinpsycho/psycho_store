<div class="container top-bottom-space">
    <h1>Feedback Wall
    	<span class='pull-right'>
        	<a class="btn btn-primary play" href= <?php echo site_url('auth/saysomething') ?> >Say Something</a>
        </span>
    </h1>
    <hr> 
    <div class="">
    	<div class="row ">
    		<?php foreach ($feedbacks as $key => $value): ?>
    			<div class="col-md-3">
    				<h4> <?php echo $value['name'] ?> - </h4>
    				<div class='well'>
    					<p> <?php echo $value['message'] ?> </p>
                        <?php if ( isset($value['products']) ): ?>
                            <?php foreach($value['products'] as $key => $product): ?>
                                <?php  echo anchor($product['product_url'], $product['product_name']) ?>
                                <br>
                            <?php endforeach; ?>
                        <?php endif; ?>
    				</div>
    			</div>
    		<?php endforeach; ?>
		</div>	
	</div>
    <a class='btn btn-default' href= <?php echo site_url('') ?> >Return To Awesomess</a>
</div>
