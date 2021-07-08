<?php echo validation_errors(); ?>
<div class="container top-bottom-space">
	<h1> Update Product Types </h1>
	<hr>
	<div class="well">
		<div class="row ">
			<div class="col-md-12">
				<form class='form-horizontal' method="post" enctype="multipart/form-data">
					
					<?php 
					$string = ''; 
					foreach ($types as $key => $value) 
						{ 
							$string .= ",$value"; 
						}
					$string = substr($string, 1);
					?>

					<div class='form-group'>

						<div class="col-md-12">
							<label for="exampleInputFile">Product Types</label>							
							<textarea name="product_types" class="form-control" placeholder="Enter Product Types" required="required"><?=$string?></textarea>
							<br>
							<b>Note: Please append a comma(,) before add new item </b>
						</div>
					</div>

				</div>			
			</div>
		</div>

		<button class='btn btn-primary' type="submit" name="submit" value="submit"> Save </button>
		<a class='btn btn-danger' href="<?=base_url().'admin/manage_product_types'?>"> Back </a>
	</form>
</div>
