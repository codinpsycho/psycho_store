<div class="modal fade" id="size_chart" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    <div class="modal-body">
      <img class='img-responsive' src= <?php echo $size_chart ?> >
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default text-center" data-dismiss="modal">Got it!</button>
    </div>
  </div>
</div>
</div>

<div class="modal fade" id="preorder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
<div class="modal-dialog" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="molot modal-title text-center" id="modal_title"> Grab this loot before it vanishes </h4>
    </div>
    <div class="modal-body">
      <h5>Ques : Why should i pre-order?<br><br>
      Ans : Look at the loot, just look at it damn it. You know how many mercenaries have been hired to snatch this loot from us and you ask why should you pre-order. Our production minions are playing with their lives here to get you this loot and for that we need your confirmation as there is a limit to everything.<br><br>So pre-order this right now and we start shipping from <strong><?php echo $restock_date?></strong>.
      </h5>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default text-center" data-dismiss="modal">Hell Yeah! I Want One.</button>
    </div>
  </div>
</div>
</div>

<div class="modal fade" id="size_preorder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
<div class="modal-dialog" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="molot modal-title text-center" id="modal_title"> Pre-Order out of Stock Sizes </h4>
    </div>
    <div class="modal-body">
      Our minions are risking their life here to bring you this loot from the dark side of the moon. Do you have any idea how many mercaneries have been hired to snatch this loot from us. So some confirmation from your side that you indeed really want your share of the loot will be a nice gesture.<br>
      Their last tranmission informed us that expected date of arrival will be <strong><?php echo $restock_date?></strong>.
      
      <br><br> So just pray that nothing goes wrong and that g-man does not get involved.
      </h5>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default text-center" data-dismiss="modal">All The Best Guys!</button>
    </div>
  </div>
</div>
</div>