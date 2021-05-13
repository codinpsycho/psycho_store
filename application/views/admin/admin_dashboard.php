<script src= <?php //echo site_url("scripts/Chart.min.js")?> ></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.css" integrity="sha512-SUJFImtiT87gVCOXl3aGC00zfDl6ggYAw5+oheJvRJ8KBXZrr/TMISSdVJ5bBarbQDRC2pR5Kto3xTR0kpZInA==" crossorigin="anonymous" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.js" integrity="sha512-OD9Gn6cAUQezuljS6411uRFr84pkrCtw23Hl5TYzmGyD0YcunJIPSBDzrV8EeCiFxGWWvtJOfVo5pOgB++Jsag==" crossorigin="anonymous"></script>

<div class="container top-bottom-space">
	<div class="well">

		<h1>Dashboard</h1>
		<hr>

		<div class="row">
			<div class="col-md-12">
				<form class="form-inline well" method="post">
					<div class="form-group">
						<label>Start Date: </label>
						<input type="date" class="form-control" name="start_date" id="start_date" value="<?=$start_date?>">

						<label>End Date: </label>
						<input type="date" class="form-control" name="end_date" id="end_date" value="<?=$end_date?>">

					</div>
					<button class="btn btn-primary" type="submit" name="submit" value="submit"> Get Stats</button>

				</form>
			</div>
		</div>



		<div class="row top-bottom-space">
			<div class="col-md-12">
				<h1 class="text-left">Sales </h1>
			</div>
		</div>

		<hr>

		<div class="row">
			<div class="col-md-12">
				<h3 class="text-center"> Orders - Checkouts From <?=$start_date?> - <?=$end_date?></h3>
				<h4 class="text-center"> Indicator: <span style="color: rgb(255, 0, 0)">Orders</span> - <span style="color: rgba(220,180,0,1)">Checkouts</span> </h4>
				<canvas id='sales_chart'></canvas>
				<h4 class="text-center">Total Num Orders 	: <?php echo $num_orders?> </h4>
				<h4 class="text-center">Total Products 	: <?php echo $total_products?> </h4>
				<h4 class="text-center">Avg Order per Day 	: <?php echo ($num_orders / count($date_ranges)) ?> </h4>
				<h4 class="text-center">Avg Products/Order 	: <?php echo ($total_products / $num_orders) ?> </h4>
			</div>
		</div>

		<hr>

		<div class="row">
			<div class="col-md-12">
				<h3 class="text-center"> Revenue From <?=$start_date?> - <?=$end_date?></h3>
				<canvas id='revenue_chart'></canvas>
				<h4 class="text-center">Total Revenue 	: <?php echo $total_revenue?> </h4>
				<h4 class="text-center">Avg Revenue per Day 	: <?php echo ($total_revenue / count($date_ranges)) ?> </h4>
			</div>
		</div>

		<hr>


		<div class="row">
			<div class="col-md-12">
				<h3 class="text-center"> COD/Online From <?=$start_date?> - <?=$end_date?></h3>
				<canvas id='payment_chart'></canvas>
			</div>
		</div>

		<hr>


		<div class="row">
			<div class="col-md-12">
				<h3 class="text-center"> Order Location Bar Chart </h3>
				<canvas id='states_chart'></canvas>
			</div>
		</div>

		<hr>

		<div class="row top-bottom-space">
			<div class="col-md-12">
				<h1 class="text-left">Products </h1>
			</div>
		</div>

		<hr>

		<div class="row">
			<div class="col-md-12">
				<h3 class="text-center"> Design Sales From <?=$start_date?> - <?=$end_date?></h3>
				<canvas id='design_chart'></canvas>
			</div>
		</div>

		<hr>

		<div class="row">
			<div class="col-md-12">
				<h3 class="text-center"> Product Type Sales From <?=$start_date?> - <?=$end_date?></h3>
				<canvas id='product_type_chart'></canvas>
			</div>
		</div>

		<hr>

		<div class="row top-bottom-space">
			<div class="col-md-12">
				<h1 class="text-left">Users </h1>
			</div>
		</div>

		<hr>

		<div class="row ">
			<div class="col-md-12">
				<h3 class="text-center"> Users From <?=$start_date?> - <?=$end_date?></h3>
				<h4 class="text-center"> Indicator: <span style="color: rgb(50,205,50)">Users Signup</span> - <span style="color: rgba(220,180,0,1)">Converted Users</span> </h4>
				<canvas id='users_chart'></canvas>
				<h4 class="text-center">Total Users     : <?=$total_users?> </h4>
				<h4 class="text-center">Total Converted Users   : <?=$total_converted_users?> </h4>
				<h4 class="text-center">Total Returning Users   : <?=count($returning_users)?> </h4>
				<h4 class="text-center">User with 1 order   : <?=count($user_with_one_order)?> </h4>
				<h4 class="text-center">User with 2 orders  : <?=count($user_with_two_order)?> </h4>
				<h4 class="text-center">User with 3 orders  : <?=count($user_with_three_order)?> </h4>
			</div>
		</div>


	</div>
	<a class='btn btn-default' href= <?php echo site_url('') ?> >Return To Awesomness</a>
</div>



<script type="text/javascript">	

	$(window).on('load', 
		function(){ 

			<?php if(isset($users)) { ?>

				var user_data_chart = {
					labels: <?php echo json_encode($joining_date)?>,
					datasets: 
					[
					{
						label: "Users Signup",
						backgroundColor: "rgba(50,205,50)",						
						data: <?php echo json_encode($users)?>
					},
					{
						label: "Converted Users",
						backgroundColor: "rgba(220,180,0,1)",						
						data: <?php echo json_encode($converted_users)?>
					}
					]
				};



				var options = {
					scales: {
						yAxes: [{
							ticks: {
								beginAtZero: true
							}
						}]
					}
				};


				var user_canvas = document.getElementById("users_chart").getContext("2d");
				var user_line_chart = new Chart(user_canvas, {
					type: 'bar',
					data: user_data_chart,
					options: options
				});


			<?php } ?>


			<?php if(isset($design_sold)) { ?>

				var design_data_chart = {
					labels: <?php echo json_encode($design)?>,
					datasets: 
					[
					{
						label: "Design Sales Dataset",
						backgroundColor : <?php echo json_encode($design_color)?>,
						data: <?php echo json_encode($design_sold)?>
					}
					]
				};

				var options = {
					scales: {
						xAxes: [{
							ticks: {
								beginAtZero: true
							}
						}]
					}
				};

				var design_canvas = document.getElementById("design_chart").getContext("2d");
				var myBarChart = new Chart(design_canvas, {
					type: 'horizontalBar',
					data: design_data_chart,
					options: options
				});


			<?php } ?>


			<?php if(isset($product_types)) { ?>


				product_type_chart_data = {
					"labels":<?php echo json_encode($product_types) ?>,
					"datasets":[{
						"label":"Product Type Sales Dataset",
						"data":<?php echo json_encode($product_types_total) ?>,
						"backgroundColor":<?php echo json_encode($product_types_color) ?>
					}]};


					var product_type_canvas = document.getElementById("product_type_chart").getContext("2d");
					var product_type_pie_chart = new Chart(product_type_canvas,{
						type: "pie",
						data: product_type_chart_data
					});




				<?php } ?>

				<?php if(isset($sales_data)) { ?>

					var options = {
						scales: {
							yAxes: [{
								ticks: {
									beginAtZero: true
								}
							}]
						}
					};

					var sales_data = {
						labels: <?php echo json_encode($sales_dates)?>,
						datasets: 
						[
						{
							label: "Orders",
							backgroundColor: "#FF0000",
							borderColor: "#FF0000",
							borderWidth:1,
							fill:false,
							data: <?php echo json_encode($sales_data)?>
						},
						{
							label: "Checkouts",
							backgroundColor: "#38E425",
							borderColor: "#38E425",							
							borderWidth:1,
							fill:false,
							data: <?php echo json_encode($checkouts)?>
						}
						]
					};

					var sales_canvas = document.getElementById("sales_chart").getContext("2d");
					var sale_line_chart = new Chart(sales_canvas,{
						"type":"bar",
						"data":sales_data,
						"options":options
					});




				<?php } ?>


				<?php if(isset($revenue_data)) { ?>
					var revenue_data = {
						labels: <?php echo json_encode($sales_dates)?>,
						datasets: 
						[
						{
							label: "Revenue Dataset",
							borderColor : "#FFFF00",
							data: <?php echo json_encode($revenue_data)?>
						}
						]
					};



					var options = {
						scales: {
							yAxes: [{
								ticks: {
									beginAtZero: true
								}
							}]
						}
					};


					var revenue_canvas = document.getElementById("revenue_chart").getContext("2d");        
					var states_data_chart = new Chart(revenue_canvas, {
						type: 'line',
						data: revenue_data,
						options: options
					});


				<?php } ?>


				<?php if(isset($online_orders)) { ?>


					payment_data = {
						"labels":["Cash On Delivery", "Online"],
						"datasets":[{
							"label":"Product Type Sales Dataset",
							"data": <?php echo json_encode($payment_modes) ?>,
							"backgroundColor":["#FF5A5E", "#46BFBD"]
						}]};


						var payment_types = document.getElementById("payment_chart").getContext("2d");
						var product_type_pie_chart = new Chart(payment_types,{
							type: "pie",
							data: payment_data
						});


					<?php } ?>

					<?php if(isset($states_sales)) { ?>


						var options = {
							scales: {
								yAxes: [{
									ticks: {
										beginAtZero: true
									}
								}]
							}
						};


						var states_data = {
							"labels": <?php echo json_encode($states)?>,
							"datasets":[{
								"label":"Order Location Dataset",
								"data": <?=json_encode($states_sales)?>,
								"fill":false,
								"backgroundColor":<?=json_encode($states_colors)?>,
								"borderColor":<?=json_encode($states_colors)?>,
								"borderWidth":1}]};



								var states_canvas = document.getElementById("states_chart").getContext("2d");   
								var states_data_chart = new Chart(states_canvas,{
									"type":"bar",
									"data": states_data,
									"options":options
								});


							<?php } ?>






						});




					</script>
