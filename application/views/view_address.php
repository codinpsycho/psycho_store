<div class="container top-bottom-space">
	<h1>Select an Address <span class='pull-right'><?php echo anchor("auth/register_address/", 'Add Address', "class='btn btn-default play navbar-btn' "); ?></span> </h1>
	<hr>
	<div class="well">
		<form method = 'post' action = "<?php echo site_url('checkout/save_address')?>" role="form">
		<div class="row">
			<?php
			foreach($addresses as $address): 	
				$complete_add = $address['first_name'].' '.$address['last_name'].'<br>'.$address['address_1'] .',<br>';
				if($address['address_2'] != NULL)
				 	$complete_add = $complete_add.$address['address_2'].', ';
				 $complete_add = $complete_add.$address['city'].'<br>'.$address['state'].' '.$address['pincode'].', '.$address['country'].'<br>'. $address['phone_number'];
			?>
			<div class="col-md-3">				
				<input type = 'radio' name = 'address_id' checked value = <?php echo $address['address_id'] ?> >
				<?php echo $complete_add ?>

				<br>
				<a href="<?=base_url().'checkout/address/edit/'.$address['address_id']?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>EDIT</a>
			</div>
			<?php
			endforeach;
			?>
		</div>
		<?php if(count($addresses) == 0): ?>
			<h3 class = 'text-center'>No Address Found</h3>
		<?php endif; ?>
	</div>
		<?php if(count($addresses) > 0): ?>
			<button class="btn btn-primary" type="submit">Review Order</button>
		<?php endif; ?>	
	</form>
</div>
