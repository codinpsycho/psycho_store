<script type="text/javascript">
  function update_image(path)
  {
    prod_img = document.getElementById('prod_img');
    if(prod_img)
    {

      prod_img.setAttribute("src", path);
    }
  }
</script>

<img class="img-responsive" alt = "<?php echo $img_alt ?>" id="prod_img" src = <?php echo site_url($images[0]) ?> >


<?php if(count($images) > 1): ?>
<div class="row">
  <div class="col-md-12">    
    <?php foreach ($images as $key => $img): ?>
      <?php $img_path = site_url($img); ?>
      <a  onclick="update_image('<?php echo $img_path ?>');" href="#prod_img"> <img width="60" id="prod_img" class="" src = <?php echo $img_path ?> > </a>
    <?php endforeach; ?> 
  </div>
</div>
<?php endif; ?>

