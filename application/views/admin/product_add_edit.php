<?php echo validation_errors(); ?>
<div class="container top-bottom-space">
	<h1> Product Add/Edit </h1>
	<hr>
	<div class="well">
		<div class="row ">
			<div class="col-md-12">
				<form class='form-horizontal' method="post" enctype="multipart/form-data" action= <?php echo $action ?> >
					<div class='form-group' >	

						<div class='col-md-2'>
							<select class= "form-control" name="category_id" required="">
								
								<option disabled="">Select Category</option>
								<?php 
								foreach ($categories as $key => $value) {	?>

									<option value="<?=$value['id']?>" <?=$value['id'] == $category_id ? 'selected' : '' ?>  ><?=$value['name']?></option>

								<?php } ?>
								
							</select>
						</div>


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
							<input class='form-control' type="text" placeholder="Game Name" name="game_name" value = "<?php echo !empty($game) ? $game : set_value('game_name'); ?>" ></input>
						</div>					
						<div class='col-md-3'>
							<input class='form-control' type="text" placeholder="Product Name" name="product_name" value = "<?php echo !empty($name) ? $name : set_value('product_name'); ?>"></input>
						</div>
						<div class='col-md-3'>
							<input class='form-control' type="text" placeholder="URL Keywords" name="url" value = "<?php echo !empty($product_url) ? $product_url : set_value('url'); ?>"></input>
						</div>
					</div>
					<div class='form-group '>
						<div class="col-md-12">
							<textarea class='form-control' name='intro' placeholder='Product Intro'><?php echo !empty($intro) ? $intro : set_value('intro'); ?></textarea>
						</div>
					</div>				
					<div class='form-group '>
						<div class="col-md-12">
							<textarea rows='8' class='form-control' name='desc' placeholder='Product Description'><?php echo !empty($desc) ? $desc : set_value('desc'); ?></textarea>
						</div>
					</div>
					<div class='form-group '>
						<div class="col-md-4">
							<input class='form-control' type="text" placeholder="Image path with '/'" name="image_path" value = "<?php echo !empty($image_path) ? $image_path : set_value('image_path'); ?>"></input>
						</div>
						<div class="col-md-2">
							<input class='form-control' type="number" placeholder="Price" name="price" value = '<?php echo !empty($price) ? $price : set_value('price'); ?>'></input>
						</div>
						<div class='col-md-1'>
							<input class='form-control' type="number" placeholder="Small Qty" name="s_qty" value = '<?php echo !empty($s_qty) ? $s_qty : set_value('s_qty'); ?>'></input>
						</div>
						<div class='col-md-1'>
							<input class='form-control' type="number" placeholder="Medium Qty" name="m_qty" value = '<?php echo !empty($m_qty) ? $m_qty : set_value('m_qty');?>'></input>
						</div>
						<div class='col-md-1'>
							<input class='form-control' type="number" placeholder="Large Qty" name="l_qty" value = '<?php echo !empty($l_qty) ? $l_qty : set_value('l_qty'); ?>'></input>
						</div>
						<div class='col-md-1'>
							<input class='form-control' type="number" placeholder="XL Qty" name="xl_qty" value = '<?php echo !empty($xl_qty) ? $xl_qty : set_value('xl_qty');?>'></input>
						</div>	

						<div class='col-md-2'>
							<select class= "form-control " name="featured">
								<option value = "1" <?=$featured == '1' ? 'selected' : ''?> >Featured</option>
								<option value = "0" <?=$featured == '0' ? 'selected' : ''?> >Not Featured</option>
							</select>
						</div>

					</div>				
				</div>			
			</div>	
			<div class="row ">
				<div class="col-md-12">

					<div class="mb-3">
						<input class='form-control mb-3' type="number" min="0" placeholder="No. of Input" name="noofinputs">
						<button class='btn btn-success' type="submit" name="addgallery" value="addgallery"> Add Gallery </button>
					</div>
					<div id="addhtml">
						<?php

						if(isset($galleries)) 
						{

							if(count($galleries) > 0) 
							{
								foreach ($galleries as $key => $value) 
								{
									?>

									<div class="position-relative pr-3 mb-3">
										<input class='form-control' type="text" placeholder="Paste Your Link Here" name="galleries[]" value="<?=$value['imgurl']?>">
										<a href="javascript:void(0)" class="delicn">Remove</a>
									</div>

									<?php
								}
							}
						}

						?>

						<?php
						if(isset($noofinputs))
						{
							for ($x = 0; $x < $noofinputs; $x++)
							{
								?>
								<div class="position-relative pr-3 mb-3">
									<input class='form-control' type="text" placeholder="Paste Your Link Here" name="galleries[]">
									<a href="javascript:void(0)" class="delicn">Remove</a>
								</div>
								<?php
							}
						}
						?>

							<!-- <div class="position-relative pr-3 mb-3">
								<input class='form-control' type="text" placeholder="Paste Your Link Here" name="galleries[]">
								<a href="javascript:void(0)" class="delicn">Remove</a>
							</div>	
							<div class="position-relative pr-3 mb-3">
								<input class='form-control' type="text" placeholder="Paste Your Link Here" name="galleries[]">
								<a href="javascript:void(0)" class="delicn">Remove</a>
							</div> -->	
						</div>	
					</div>
				</div>
			</div>

			<button class='btn btn-primary' type="submit"> Save Product </button>
		</form>
	</div>


	<script type="text/javascript">

		$(document).on('click', '#addgallery', function(e) {

			var addhtml = '<div class="position-relative pr-3 mb-3"><input class="form-control" type="text" placeholder="Paste Your Link Here" name="galleries[]" value="56"><a href="javascript:void(0)" class="delicn">Remove</a></div>';

			$('#addhtml').append(addhtml);
		});

		$(document).on('click', '.delicn', function(e) {
			$(this).parent().remove();
		});

	</script>