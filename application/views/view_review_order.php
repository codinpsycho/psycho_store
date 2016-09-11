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
		var new_price = parseInt(<?php echo $this->cart->total() + 60 ?>);
		update_price_text(new_price);
	}

	function update_for_online()
	{
		var new_price = parseInt(<?php echo $this->cart->total()?>);
		update_price_text(new_price);
	}

	function update_price_text(price)
	{
		var actual_price = document.getElementById('actual_price');
		actual_price.innerHTML = "Actual Price : <i class='fa fa-rupee'></i> " + price;

		var header_price = document.getElementById('header_price');
		header_price.innerHTML = "Confirm Order <span class='pull-right'> <i class='fa fa-rupee'></i> <span id='price'>" +  price;

		var final_price = document.getElementById('final_price');
		final_price.innerHTML = "Final Price : <i class='fa fa-rupee'></i> " +  price;

		var button_price = document.getElementById('button_price');
		button_price.innerHTML = "Place Order | <i class='fa fa-rupee'></i> " +  price + " <i class='fa fa-arrow-right'>";
	}
</script>

<div class="container top-bottom-space">
	<div class="row">
		<div class="col-md-12">
			<h1 id='header_price'>Order Review
			<span class="pull-right"> <i class="fa fa-rupee"></i> <span id='price'> <?php echo $this->cart->final_price()?></span></span> 
			</h1>
		</div>
	</div>
	<hr>
	<div class="well">
		<div class="row">			
<!-- 			<?php //if($shipping_available == false): ?>
				<div class="col-md-4">
					<h1>Shipping To</h1>
					<h4> <?php // echo $address;?> </h4>
				</div>
				<div class="col-md-8">
					<h1>Sorry</h1>
					<p> Note : We have no idea where your realm is. We have deployed our scout minions in search of your address. But until then go back and try some other adrress, or send us a mail at <a href="mailto:contact@psychostore.in">contact@psychostore.in</a> for a personal delivery (no extra charges).</p>
				</div>
			<?php// else: ?> -->			
			<div class="col-md-4">
				<h1>Shipping To</h1>
				<h4> <?php echo $address;?> </h4>
			</div>
			<div class="col-md-4">
				<h1>Pricing
					<h4 id='actual_price'>Actual Price : <i class="fa fa-rupee"></i> <?php echo $this->cart->total() ?></h4>
					<h4 id='discount'>Discount : <i class="fa fa-rupee"></i> <?php echo $this->cart->discount() ?> </h4>
					<h4 id='shipping'>Shipping : Always Free </h4>
					<h4 id='final_price'>Final Price : <i class="fa fa-rupee"></i> <?php echo $this->cart->final_price() ?> </h4>
				</h1>
			</div>
			<div class="col-md-4">
				<h1>Payment Mode</h1>
				<form  method = "post" action = <?php echo site_url('checkout/payment')?> role="form" onchange="update_price(this)">
					<select class="form-control" name="payment_mode" onchange="update_price(this)">
						<option value="pre-paid" >Pay Online</option>
						<?php if($cod_available == true): ?>
						<option value="cod">Cash On Delivery (+60 INR) </option>
						<?php endif; ?>
					</select>
				<?php if($cod_available == false): ?>
						<p> Note : Cash On Delivery Service not available for your address</p>
				<?php endif; ?>			
			</div>
			<?php //endif; //We dont deliver at this address ?>	
		</div>
	</div>
	Placing the order implies you agree to our <a target="_blank" href = <?php echo site_url('shipping_returns') ?> > 365 days Shipping and Returns policy </a>
	<?php //if($shipping_available == true): ?>
		<button class="btn btn-primary pull-right" type="submit" id='button_price'> Place Order | <i class="fa fa-rupee"></i>  <?php echo $this->cart->final_price();?> <i class="fa fa-arrow-right"></i></button>
	<?php //endif; ?>	
	</form>
</div>
