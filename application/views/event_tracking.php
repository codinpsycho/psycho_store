<script type="text/javascript">
	function add_to_cart()
	{
		var add_cart_btn = document.getElementById('add_to_cart');
		if(add_cart_btn)
		{
			add_cart_btn.addEventListener('click', function()
			{
				ga('send', 'event', 'cart', 'add', {'nonInteraction': 1} );
				fbq('track', 'AddToCart');
			});
		}
	}

	function apply_discount()
	{
		var apply_disc_btn = document.getElementById('apply_discount');		
		if(apply_disc_btn)
		{
			apply_disc_btn.addEventListener('click', function()
			{
				ga('send', 'event', 'cart', 'discount');
			});
		}
	}

	function checkout()
	{
		var checkout_btn = document.getElementById('checkout');		
		if(checkout_btn)
		{
			checkout_btn.addEventListener('click', function()
			{
				ga('send', 'event', 'cart', 'checkout', {'nonInteraction': 1} );
				fbq('track', 'InitiateCheckout');
			});
		}
	}	
</script>

<script type="text/javascript">
	//Call google analytics event tracking functions
	add_to_cart();
	apply_discount();
	checkout();
</script>