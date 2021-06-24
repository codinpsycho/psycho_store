<?php echo validation_errors(); ?>
<div class="container top-bottom-space">
	<h1> Category Add/Edit </h1>
	<hr>
	<div class="well">
		<div class="row ">
			<div class="col-md-12">
				<form class='form-horizontal' method="post" enctype="multipart/form-data">
					

					<div class='form-group'>

						<div class="col-md-6">
							<label for="exampleInputName">Category Name</label>
							<input class='form-control' type="text" placeholder="Enter Category Name" name="name" id="exampleInputName" value = '<?php echo !empty($category['name']) ? $category['name'] : set_value('name'); ?>'></input>
						</div>

						<div class="col-md-6">
							<label for="exampleInputFile">File Upload</label>
							<input class='form-control' type="file" name="upload" id="exampleInputFile" accept="image/*" >
							<p class="help-block">You can selet one file at a time.</p>
						</div>

					</div>

				</div>			
			</div>
		</div>

		<button class='btn btn-primary' type="submit" name="submit" value="submit"> Save Category </button>
		<a class='btn btn-danger' href="<?=base_url().'admin/manage_category';?>"> Cancel </a>
	</form>
</div>
