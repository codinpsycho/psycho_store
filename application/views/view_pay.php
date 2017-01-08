<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<div class="container top-bottom-space">
	<h1>No Cash, No Issues</h1>
	<hr>
	<div class="well">
		<div class="row">
			<div class="col-md-4">
				<form id="payment_form" method = "post" action = <?php echo site_url("pay/process_payment")?> role="form">
				  <div class="form-group">
				    <div class="input-group">
				      <div class="input-group-addon"><i class="fa fa-rupee"></i></div>
				      <input type="number" class="form-control" id="amount" name="pay_amount" placeholder="Amount">
				    </div>
				  </div>				  
				</form>
				<button id="pay_btn" class="btn btn-primary">Pay Securely</button>
			</div>			
		</div>
	</div>
</div>


<script>

document.getElementById('pay_btn').onclick = process_order_payment

function process_order_payment()
{
	var amount = parseInt(document.getElementById("amount").value) *100;
	var options = {
	    "key": "<?php echo $this->config->item('rzp_merchant_key') ?>",
	    "amount": parseInt(document.getElementById("amount").value) * 100, // 2000 paise = INR 20
	    "name": "Psycho Store",
	    
	    "handler": payment_authorized,
	    "prefill": {	        
	        "email": "ishkaran.singh@hotmail.com",
	        "contact": "7387045828"
	    },
	    "theme": {
	        "color": "#09f"
	    }
	};

	var rzp = new Razorpay(options);
	rzp.open();
}

function payment_authorized(response)
{
	// Form reference:
	var the_form = document.getElementById('payment_form');
	// Add rzp_payment_id
	console.log(the_form);
	console.log(response);
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