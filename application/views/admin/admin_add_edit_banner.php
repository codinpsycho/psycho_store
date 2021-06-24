<?php echo validation_errors(); ?>
<div class="container top-bottom-space">
	<h1> Banner Add/Edit </h1>
	<hr>
	<div class="well">
		<div class="row ">
			<div class="col-md-12">
				<form class='form-horizontal' method="post" enctype="multipart/form-data">
					

					<div class='form-group'>

						<div class="col-md-6">
							<input class='form-control' type="number" min="1" placeholder="Enter Order Number" name="sort" value = '<?php echo !empty($banner['sort']) ? $banner['sort'] : set_value('sort'); ?>' required></input>
						</div>

						<div class="col-md-6">
							<select class='form-control' name="is_active">
								<option value="1">Active</option>
								<option value="0">Inactive</option>
							</select>
						</div>

					</div>

					<div class='form-group'>

						<div class="col-md-6">
							<label for="exampleInputFile">File Upload</label>
							<input class='form-control' type="file" name="uploads[]" multiple="" accept="image/*" >
							<p class="help-block">You can select multiple files at a time.</p>
						</div>

					</div>

				</div>			
			</div>
		</div>

		<button class='btn btn-primary' type="submit" name="submit" value="submit"> Save </button>
		<a class='btn btn-danger' href="<?=base_url().'admin/banners'?>"> Cancel </a>
	</form>
</div>
