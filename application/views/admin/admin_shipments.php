<div class="loader-bg" style="display: none;">
    <div class="loader"></div>
</div>

<div class="container top-bottom-space">  
    <h1> processing Shipments
    	<span class="pull-right navbar-text play"> <small><?php echo $num_pkg_shipments?> shipment(s) pending for pickup on <?php echo $date ?></small></span>
    </h1>
    <hr>
    <div class="well">
        <div class="row">
            <div class="col-md-12">
                
                
                <!-- <a class="btn btn-primary navbar-btn" <?php //echo $pickup_btn_state?> href= <?php //echo site_url('admin/request_pickup') ?> >Request Pickup (<?php //echo $num_pkg_shipments?>)</a> -->
                <!-- <a target="_blank" class="btn btn-primary navbar-btn" href= <?php //echo site_url('admin/manifest') ?> >Manifest</a> -->
                

                <?php if($pickup_requested): ?>
                    <h3 class="navbar-text text-primary pull-right play"> Pickup Requested for <?php echo $num_pcikup_shipments ?> shipments. Make sure they are ready.</h3>
                    <?php else: ?>
                        <h3 class="navbar-text text-danger pull-right play"> No Pickup has been requested. </h3>
                    <?php endif; ?>
                </div>
            </div>
        </div>    
        <hr>
        <div class="well">        
         <div class="row ">
          <div class="col-md-12">
           <?php echo $orders_table; ?>
       </div>
   </div>
</div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Enter Tracking Detail #<span id="txnidM"></span></h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <textarea class="form-control" id="tracking_link" aria-describedby="emailHelp" placeholder="Paste Your Tracking Link Here" style="resize: none;"></textarea>
                    <input type="hidden" id="url" value="<?=base_url().'admin/addTrackingDetail'?>">
                    <input type="hidden" id="txnid">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save">Send Mail</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $('.exampleModal').on('click', function() {
        var $el = $(this);
        var $txnid = $(this).data('txnid');
        var $txnid = $(this).data('txnid');
        $('#txnidM').html($txnid);
        $('#txnid').val($txnid);
    });


    $('#save').click(function (e) {
        
        var tracking_link = $('#tracking_link').val();
        var txnid = $('#txnid').val();
        var url = $('#url').val();

        if(tracking_link.length > 1 ) {
            
            $('.loader-bg').show();

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: { tracking_link: tracking_link, txnid: txnid },
            })
            .done(function(resposne) {
                if(resposne.success == true) {
                    alert(resposne.message);
                }

            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {

                $('.loader-bg').hide();
                $('#exampleModal').modal('toggle');
                window.location.reload();
            });

        } else {

            alert('Please paste the order tracking link first!');
        }

    });
</script>