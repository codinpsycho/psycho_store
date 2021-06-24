<?php echo validation_errors(); ?>
<div class="container top-bottom-space">
	<h1> Category Game Add/Edit </h1>
	<hr>
	<div class="well">
		<div class="row ">
			<div class="col-md-12">
				<form class='form-horizontal' method="post" enctype="multipart/form-data">
					

					<div class='form-group'>

						<div class="col-md-4">
							<label for="exampleSelect">Parent Category</label>
							<select class='form-control' id="exampleSelect" name="category_id">
								<?php foreach($categories as $value) { ?>
									<option value="<?=$value['id']?>" 
										<?= isset($subcategory) ? ($subcategory['category_id'] == $value['id'] ? 'selected' : '') : '' ?> >
										<?=$value['name'] ?>
									</option>
								<?php } ?>
							</select>
						</div>

						<div class="col-md-4">
							<label for="exampleInputName">Game Name</label>
							<input class='form-control' type="text" placeholder="Enter Game Name" name="game_name" id="exampleInputName" value = '<?php echo !empty($subcategory['game_name']) ? $subcategory['game_name'] : set_value('game_name'); ?>' required></input>
						</div>

						<div class="col-md-4">
							<label for="exampleInputFile">File Upload</label>
							<input class='form-control' type="file" name="upload" id="exampleInputFile" accept="image/*" >
							<p class="help-block">You can selet one file at a time.</p>
						</div>

					</div>

				</div>			
			</div>
		</div>

		<button class='btn btn-primary' type="submit" name="submit" value="submit"> Save Category </button>
		<a class='btn btn-danger' href="<?=base_url().'admin/subcategory';?>"> Cancel </a>
	</form>
</div>
