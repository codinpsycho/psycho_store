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
		var new_price = parseInt(<?php echo ($this->cart->final_price() - $points_claimed) + $cod_charges ?>);
		update_price_text(new_price);
	}
	function update_for_online()
	{
		var new_price = parseInt(<?= ($this->cart->final_price() - $points_claimed) ?>);
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

<div class="container top-bottom-space">
	<div class="row">
		<div class="col-md-12">
			<h1 id='header_price'>Order Review
				<span class="pull-right"> <i class="fa fa-rupee"></i> <span id='price'> <?= ($this->cart->final_price() - $points_claimed)?></span></span> 
			</h1>
		</div>
	</div>
	<hr>
	<div id="alert"></div>
	<div class="well">
		<div class="row">

			<!-- <?php //if($shipping_available == false): ?>
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
				<h4> <?php echo $formatted_address;?> </h4>
				<a href="<?=base_url().'checkout/address?edit=true'?>">Click to Change address</a>
			</div>
			<div class="col-md-4">
				<h1>Pricing
					<h4 id='actual_price'>Actual Price : <i class="fa fa-rupee"></i> <?php echo $this->cart->total() ?></h4>
					<h4 id='discount'>Discount : <i class="fa fa-rupee"></i> <?php echo $this->cart->discount() ?> </h4>
					<h4 id='shipping'>Shipping : Always Free </h4>
					<h4 id='final_price'>Final Price : <i class="fa fa-rupee"></i> <?= ($this->cart->final_price() - $points_claimed) ?> </h4>

					<?php 
					if($this->config->item('redeem_points'))	{
						?>
						<h4>Points Claimed : <i class="fa fa-rupee"></i> <?=$points_claimed?> </h4>

						<form action="" method="post">
							<input type="hidden" name="is_clicked" value="1">
							<input type="checkbox" name="is_applied" value="1" onClick='submit();' <?=$checked == 1 ? 'checked' : ''?>> Click to claim your points
						</form>
					<?php } ?>

				</h1>
			</div>
			<div class="col-md-4">
				<h1>Payment Mode</h1>
				<form id="payment_mode_form"  method = "post" action = "<?php echo site_url('checkout/payment')?>" role="form">

					<input type="hidden" name="points_claimed" value="<?=$points_claimed?>">

					<select class="form-control" id="payment_mode_select" name="payment_mode" onchange="update_price(this)">
						<option value="pre-paid" >Pay Online</option>
						<?php if($cod_available == true): ?>
							<option value="cod">Cash On Delivery( ₹60 extra )</option>
						<?php endif; ?>
					</select>
				</form>
				<?php if($cod_available == false): ?>
					<p> Note : Cash On Delivery Service not available for your address</p>
				<?php endif; ?>			
			</div>
			<?php //endif; //We dont deliver at this address ?>	
		</div>
	</div>
	Placing the order implies you agree to our <a target="_blank" href = <?php echo site_url('shipping_returns') ?> > 365 days Shipping and Returns policy </a>
	<button class="btn btn-primary pull-right" id='place_order_btn'> Place Order | <i class="fa fa-rupee"></i>  <?=($this->cart->final_price() - $points_claimed);?> <i class="fa fa-arrow-right"></i></button>

	<?php $data['tag_name'] = 'psychofamous'; echo $this->load->view('view_product_instagram', $data, TRUE); ?>
	
</div>

<script>
	var options = {
		
		"key": "<?php echo $this->config->item('rzp_merchant_key') ?>",
    "amount": "<?= ($this->cart->final_price() - $points_claimed)*100; ?>", // 2000 paise = INR 20
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