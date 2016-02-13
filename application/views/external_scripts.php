<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
</script>

<?php
if(isset($product))
{
  $disqus_url = base_url()."product/$product[product_id]";
}
else
{
  $disqus_url = current_url();
}
?>

<script type="text/javascript">  
  var disqus_shortname = 'psychostore';
  var disqus_url = '<?php echo $disqus_url; ?>';
</script>

<script>(function(d, s, id)
{
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=601282446622582";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>

<script src="https://apis.google.com/js/platform.js" async defer></script>
<script type="text/javascript" async defer src="//assets.pinterest.com/js/pinit.js"></script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-32790740-2', 'auto');
  ga('send', 'pageview');
</script>

<script type="text/javascript" src= <?php echo site_url("scripts/instafeed.min.js") ?> ></script>