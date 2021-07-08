<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script type="text/javascript">
	function update_price(select)
	{
		if(select.options[select.selectedIndex].value == 'cod')
		{
			update_for_cod();
		}
		else if(select.options[select.selectedIndex].value == 'pre-paid')
		{
			update_for_online();
		}
	}
	function update_for_cod()
	{
		var new_price = parseInt(<?= ($this->cart->final_price() - $points_claimed) + $cod_charges; ?>);
		update_price_text(new_price);
	}
	function update_for_online()
	{
		var new_price = parseInt(<?= ($this->cart->final_price() - $points_claimed) - $prepaid_discount; ?>);
		update_price_text(new_price);
	}
	function update_price_text(price)
	{
		// var actual_price = document.getElementById('actual_price');
		// actual_price.innerHTML = "Actual Price : <i class='fa fa-rupee'></i> " + price;
		var header_price = document.getElementById('header_price');
		header_price.innerHTML = "Order Review <span class='pull-right'> <i class='fa fa-rupee'></i> <span id='price'>" +  price;
		var final_price = document.getElementById('final_price');
		final_price.innerHTML = "Final Price : <i class='fa fa-rupee'></i> " +  price;

		var button_price = document.getElementById('place_order_btn');
		button_price.innerHTML = "Place Order | <i class='fa fa-rupee'></i> " +  price + " <i class='fa fa-arrow-right'>";
	}
</script>

<section class="sectionew">
	<div class="container">
		<div class="row borderrow">

			<h1 id='header_price'>Order Review
				<span class="pull-right"> <i class="fa fa-rupee"></i> <span id='price'> <?= ($this->cart->final_price() - $points_claimed) - $prepaid_discount; ?></span></span> 
			</h1>

		</div>
		<div class="row">
			<div class="col-md-12 col-lg-12">
				<div class="row rowbg">
					<div class="col-md-8 col-lg-8 col-xl-8 bgleft">
						<div class="blockscroll">

							<?php foreach ($checkout_items as $key => $value) { ?>
								
								<div class="d-flex productdetailsrow">
									<div class="prodimg">
										<img src="<?=base_url().$value['product']['product_image_path']?>" alt="psychostore">
									</div>
									<div class="prodetails">
										<h3><?=$value['product']['product_name']?></h3>
										<div class="d-flex">
											<div class="mr-3">
												<h4>Size</h4>
												<p><?=$value['option']?></p>
											</div>
											<div>
												<h4>Quantity</h4>
												<p><?=$value['count']?></p>
											</div>
										</div>
										<div class="prodprice">
											<span class="mr-1"><i class="fa fa-rupee"></i></span><span><?=$value['product']['product_price']?></span>
										</div>
									</div>
								</div>
								
							<?php } ?>

						</div>
					</div>
					<div class="col-md-4 col-lg-4 col-xl-4 bgright">
						<div class="proaddress mt-5">

							<div class="mb5" style="margin-bottom:20px">
								<h2>Payment Method</h2>
								<form id="payment_mode_form"  method = "post" action = "<?php echo site_url('checkout/payment')?>" role="form">
									

									<input type="hidden" name="points_claimed" value="<?=$points_claimed?>">

									
									<!-- dev on 07.07.2021 -->
									<input type="hidden" name="prepaid_discount" value="<?=$prepaid_discount ?>">
									<input type="hidden" name="prepaid_final_price" value="<?= ($this->cart->final_price() - $points_claimed) - $prepaid_discount; ?>">
									<!-- dev on 07.07.2021 -->


									<select class="custom-select" id="payment_mode_select" name="payment_mode" onchange="update_price(this)">
										<option value="pre-paid" >Pay Online(Extra 5% off)</option>
										<?php if($cod_available == true): ?>
											<option value="cod">Cash On Delivery( ₹60 extra )</option>
										<?php endif; ?>
									</select>
								</form>
								<?php if($cod_available == false): ?>
									<p> Note : Cash On Delivery Service not available for your address</p>
								<?php endif; ?>	
							</div>

							<div class="mb5"  style="margin-bottom:70px">
								<button type="button" class="btn btn-primary btnorder" id='place_order_btn'>Place order | <i class="fa fa-rupee"></i>  
									<?= ($this->cart->final_price() - $prepaid_discount) - $points_claimed;?> <i class="fa fa-arrow-right"></i></button>
								</div>

								<div class="mb-5">
									<h2>Pricing</h2>
									<p id='actual_price'>Actual Price : <i class="fa fa-rupee"></i> <?php echo $this->cart->total() ?></p> 
									<p id='discount'>Discount : <i class="fa fa-rupee"></i> <?php echo $this->cart->discount() ?></p> 
									<p id='shipping'>Shipping : Always free</p> 
									<p id='final_price'>Final Price : <i class="fa fa-rupee"></i> <?= ($this->cart->final_price() - $points_claimed) - $prepaid_discount; ?></p>

									<?php if($this->config->item('redeem_points'))	{	?>
										<p>Points Claimed : <i class="fa fa-rupee"></i> <?=$points_claimed?> </p>
										<form action="" method="post">											
											<input type="hidden" name="is_clicked" value="1">
											<input type="checkbox" name="is_applied" value="1" onClick='submit();' <?=$checked == 1 ? 'checked' : ''?>> Click to claim your points
										</form>
									<?php } ?>

								</div>


								<div class="mb-5">
									<h2>Shipping address</h2>
									<p><?=$formatted_address;?></p>
									<a href="<?=base_url().'checkout/address?edit=true'?>">Click to Change address</a>
								</div>


							</div>

						</div>
					</div>
				</div>

			</div>

		</div>
	</section>

	<script type="text/javascript">

		var options = 
		{

			"key": "<?php echo $this->config->item('rzp_merchant_key') ?>",
		"amount": "<?= (($this->cart->final_price() - $points_claimed) - $prepaid_discount) * 100; ?>", // 2000 paise = INR 20
		"name": "Psycho Store",

		"handler": payment_authorized,
		"prefill": {
			"name": "<?php echo $raw_address['first_name'].' '.$raw_address['last_name']; ?>",
			"email": "<?php echo $email ?>",
			"contact": "<?php echo $raw_address['phone_number']; ?>"
		},
		"notes": {
			"Name": "<?php echo $raw_address['first_name'].' '.$raw_address['last_name']; ?>",
			"Txn_id": "<?php echo $txn_id; ?>",
		},
		"theme": {
			"color": "#09f"
		}
	};

	document.getElementById('place_order_btn').onclick = process_order_payment

	function process_order_payment()
	{

		select = document.getElementById('payment_mode_select');

		if(select.options[select.selectedIndex].value == 'cod')
		{
			document.getElementById('payment_mode_form').submit();
		}
		else if(select.options[select.selectedIndex].value == 'pre-paid')
		{
			var rzp = new Razorpay(options);
			rzp.open();
		}
	}

	function payment_authorized(response)
	{
		// Form reference:
		var the_form = document.getElementById('payment_mode_form');
		// Add rzp_payment_id
		addHidden(the_form, 'rzp_payment_id', response.razorpay_payment_id);
		the_form.submit();
	}

	function addHidden(theForm, key, value)
	{
    	// Create a hidden input element, and append it to the form:
    	var input = document.createElement('input');
    	input.type = 'hidden';
    	input.name = key;
    	input.value = value;
    	theForm.appendChild(input);
    }

</script>
