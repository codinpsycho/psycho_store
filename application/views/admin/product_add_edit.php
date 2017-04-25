<?php echo validation_errors(); ?>
<div class="container top-bottom-space">
    <h1> Product Add/Edit </h1>
    <hr>
    <div class="well">
    	<div class="row ">
	    	<div class="col-md-12">
	    		<form class='form-horizontal' method="post" action= <?php echo $action ?> >
				<div class='form-group' >					
					<div class='col-md-2'>
						<select class= "form-control " name="type">
							<?php if($type != ''):?>
								<option value = <?php echo $type ?> ><?php echo $type ?></option>
							<?php else: ?>
							<option value = "tshirt">Tshirt</option>
							<option value = "hoodie">Hoodie</option>
							<option value = "mobilecover">Mobile Covers</option>
							<option value = "mugs">Coffee Mugs</option>
							<option value = "posters">Poster</option>
							<?php endif;?>
						</select>
					</div>
					<div class="col-md-2">
						<input class='form-control' type="text" placeholder="Game Name" name="game_name" value = "<?php echo $game ?>" ></input>
					</div>					
					<div class='col-md-3'>
						<input class='form-control' type="text" placeholder="Product Name" name="product_name" value = "<?php echo $name ?>"></input>
					</div>
					<div class='col-md-5'>
						<input class='form-control' type="text" placeholder="URL Keywords" name="url" value = "<?php echo $product_url ?>"></input>
					</div>
				</div>
				<div class='form-group '>
					<div class="col-md-12">
						<textarea class='form-control' name='intro' placeholder='Product Intro'><?php echo $intro ?></textarea>
					</div>
				</div>				
				<div class='form-group '>
					<div class="col-md-12">
						<textarea rows='8' class='form-control' name='desc' placeholder='Product Description'><?php echo $desc ?></textarea>
					</div>
				</div>
				<div class='form-group '>
					<div class="col-md-4">
						<input class='form-control' type="text" placeholder="Image path with '/'" name="image_path" value = "<?php echo $image_path ?>"></input>
					</div>
					<div class="col-md-2">
						<input class='form-control' type="number" placeholder="Price" name="price" value = '<?php echo $price ?>'></input>
					</div>
					<div class='col-md-1'>
						<input class='form-control' type="number" placeholder="Small Qty" name="s_qty" value = '<?php echo $s_qty ?>'></input>
					</div>
					<div class='col-md-1'>
						<input class='form-control' type="number" placeholder="Medium Qty" name="m_qty" value = '<?php echo $m_qty ?>'></input>
					</div>
					<div class='col-md-1'>
						<input class='form-control' type="number" placeholder="Large Qty" name="l_qty" value = '<?php echo $l_qty ?>'></input>
					</div>
					<div class='col-md-1'>
						<input class='form-control' type="number" placeholder="XL Qty" name="xl_qty" value = '<?php echo $xl_qty ?>'></input>
					</div>				
				</div>				
			</div>			
		</div>		
	</div>
	<button class='btn btn-primary' type="submit"> Save Product </button>
	</form>
</div>
