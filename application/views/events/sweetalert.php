<script type="text/javascript" src= <?php echo site_url('scripts/sweetalert.min.js') ?> ></script>
<link rel="stylesheet" href=<?php echo site_url('css/sweetalert.css')?>>

<script type="text/javascript">
function show_sweetalert()
{
	swal({
	    title:'<?php echo $title ?>',
	    text:'<?php echo $body ?>',
	    type:'<?php echo $type ?>',        
	    confirmButtonText:'<?php echo $button_text ?>',
	    html : 'true',
	});
}

setTimeout( show_sweetalert , <?php echo $timeout; ?>);
</script>