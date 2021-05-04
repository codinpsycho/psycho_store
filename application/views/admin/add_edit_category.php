<?php echo validation_errors(); ?>
<div class="container top-bottom-space">
	<h1> Category Add/Edit </h1>
	<hr>
	<div class="well">
		<div class="row ">
			<div class="col-md-12">
				<form class='form-horizontal' method="post" enctype="multipart/form-data">
					

					<div class='form-group'>

						<div class="col-md-12">
							<input class='form-control' type="text" placeholder="Enter Category Name" name="name" value = '<?php echo !empty($category['name']) ? $category['name'] : set_value('name'); ?>'></input>
						</div>

					</div>

				</div>			
			</div>
		</div>

		<button class='btn btn-primary' type="submit" name="submit" value="submit"> Save Category </button>
	</form>
</div>
