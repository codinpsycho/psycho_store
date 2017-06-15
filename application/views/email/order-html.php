<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><title>Psycho Store | Order Placed</title></head>
<body>
<h1><strong><?php echo $username?></strong>, your order has been placed!</h1>
<br>
<h3>Order Id : <?php echo $order_id; ?></h3>
<h3>Payment Mode : <?php echo $payment_mode; ?></h3>
<p>Thank you for shopping at Psycho Store. Our very efficient minions are onto the task of processing your order. So sit back and relax. Depending on the design(s)/product(s) ordered, your order might be coming in parts, so don't worry if there's some items missing from your order, they will be coming soon in the next parcel.  Your order should reach you in next 5-8 buisness days unless G-Man gets involved somehow.</p>
<h2>This awesomeness</h2>
<?php echo $product_table; ?>
<br>
<h2>Will soon be shipped to</h2>
<p> <?php echo $address; ?> </p>
<br>
<br>
<p>Note : Please use the above order id in case of any communication/problem regarding this order.</p>
<br>
<br />

<?php  echo $this->load->view('email/signature') ?>;
</div>
</body>
</html>