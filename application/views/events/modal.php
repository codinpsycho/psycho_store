<!-- Modal -->
<div class="modal fade" id= '<?php echo $event_name?>' tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="molot modal-title text-center" id= '<?php echo $event_name."_title" ?>' >  </h4>
      </div>
      <div class="modal-body">
        <p id= '<?php echo $event_name."_body" ?>' >  </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default text-center" data-dismiss="modal"><?php echo $button_text?></button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  window.addEventListener('load', init);

  function init()
  {
    var event_name = '<?php echo $event_name?>';
    var modal_name = '#' + event_name;    
    var tile_id = event_name + '_title';
    var title = document.getElementById(tile_id);
    title.innerHTML= '<?php echo $title ?>';    
   
    var body_id = '<?php echo $event_name.'_body'?>';
    var body =  document.getElementById(body_id);
    body.innerHTML= '<?php echo $body ?>';
    
    setTimeout( function(){ $(modal_name).modal('show'); } , <?php echo $timeout; ?>);
  }
</script>
