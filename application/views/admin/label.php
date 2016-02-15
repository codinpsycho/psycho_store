<table border="1" cellpadding="5px" cellspacing="0">
<tr>
    <td align="left">
        <img  src="<?php echo $company_logo ?>">
    </td>
    <td align='center' >
        <img width='100px' src="<?php echo $courier_logo ?>">
    </td>
</tr>
<tr>
    <td >
        <img width="150" src="<?php echo $wb_barcode ?>">
    </td>
    <td align="right">
        <p><?php echo $pin ?></p>
        <h4><?php echo $coc_code ?></h4>
    </td>
</tr>
<tr>
    <td>
        <small>
        <p>Shipping address: <?php echo $address ?></p>
        <p><?php echo $city."<br>".$dispatch_center."<br>".$pin ?></p>
        </small>
    </td>
    <td align="center">
        <h1><?php echo $payment_mode ?></h1>
    </td>
</tr>
<tr>
    <td>
        <p>Gaming/Geek Merchandise</p>
    </td>
    <td align="center">
        <p>Total : <?php echo $order_amount ?></p>
    </td>
</tr>
<tr>
    <td>
        <img  src="<?php echo $oid_barcode ?>">
    </td>
    <td>
        <small><small> Return address: <?php echo $return_address ?></small></small>
    </td>
</tr>
    <td colspan="2" align='center'>
        <h5>Show off your geeky side. Use this hashtag on instagram to get featured.</h5>
        <strong><h1>#PSYCHOFAMOUS</h1></strong>
    </td>
<tr> 
</tr>
</table>
