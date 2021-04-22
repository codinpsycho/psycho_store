<!-- Owl Stylesheets -->
<link rel="stylesheet" href="<?=base_url();?>scripts/owlcarousel/assets/owl.carousel.min.css">
<link rel="stylesheet" href="<?=base_url();?>scripts/owlcarousel/assets/owl.theme.default.min.css">
<!-- javascript -->
<script src="<?=base_url();?>scripts/owlcarousel/owl.carousel.js"></script>
<script>
  $(document).ready(function() {
    $('.owl-carousel').owlCarousel({
      loop: true,
      margin: 10,
      nav:true,
      dots:false,
      responsiveClass: true,
      responsive: {
        0: {
          items: 1,

        },
        600: {
          items: 3,
        },
        1000: {
          items: 4,
        }
      }
    })
  })
</script>

<div class="row">
  <div class="col-md-12">
    <h3 class="molot">Product Galleries <small> (randomly)</small></h3>
    <hr>
  </div>
  <div class="col-md-12">  

    <div class="productslider">
     <div class="owl-carousel owl-theme">

      <?php
      
      foreach ($product_galleries as $key => $value) 
        {

      ?>
        <div class="item">
          <img src="<?=$value['imgurl']?>" class="img-responsive" alt="Product">
        </div>

      <?php

        }

      ?>


      <!-- <div class="item">
        <img src="https://miro.medium.com/max/3840/0*AhQ1F4hEelt6Zz6s.jpg" class="img-responsive" alt="">
        <p  class="text-center">Product</p>
      </div>
      <div class="item">
        <img src="https://miro.medium.com/max/3840/0*AhQ1F4hEelt6Zz6s.jpg" class="img-responsive" alt="">
        <p  class="text-center">Product</p>
      </div>
      <div class="item">
        <img src="https://miro.medium.com/max/3840/0*AhQ1F4hEelt6Zz6s.jpg" class="img-responsive" alt="">
        <p  class="text-center">Product</p>
      </div>
      <div class="item">
        <img src="https://miro.medium.com/max/3840/0*AhQ1F4hEelt6Zz6s.jpg" class="img-responsive" alt="">
        <p  class="text-center">Product</p>
      </div>
      <div class="item">
        <img src="https://miro.medium.com/max/3840/0*AhQ1F4hEelt6Zz6s.jpg" class="img-responsive" alt="">
        <p  class="text-center">Product</p>
      </div>
      <div class="item">
        <img src="https://miro.medium.com/max/3840/0*AhQ1F4hEelt6Zz6s.jpg" class="img-responsive" alt="">
        <p  class="text-center">Product</p>
      </div>
      <div class="item">
        <img src="https://miro.medium.com/max/3840/0*AhQ1F4hEelt6Zz6s.jpg" class="img-responsive" alt="">
        <p  class="text-center">Product</p>
      </div>
      <div class="item">
        <img src="https://miro.medium.com/max/3840/0*AhQ1F4hEelt6Zz6s.jpg" class="img-responsive" alt="">
        <p  class="text-center">Product</p>
      </div>
      <div class="item">
        <img src="https://miro.medium.com/max/3840/0*AhQ1F4hEelt6Zz6s.jpg" class="img-responsive" alt="">
        <p  class="text-center">Product</p>
      </div>
      <div class="item">
        <img src="https://miro.medium.com/max/3840/0*AhQ1F4hEelt6Zz6s.jpg" class="img-responsive" alt="">
        <p  class="text-center">Product</p>
      </div> -->

    </div>
  </div>


</div>
</div>