<html xmlns="http://www.w3.org/1999/xhtml"
xmlns:og="http://ogp.me/ns#"
xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<title>Psycho Store</title>
<meta property="fb:app_id" content="601282446622582" />
<meta property="fb:admins" content="100001096628321"/>
<meta property="og:title" content="Psycho Store"/>
<meta property="og:type" content="Clothing"/>
<meta property="og:url" content="http://localhost/psycho_store/"/>
<meta property="og:image" content=""/>
<meta property="og:description" content="Description of page content" />
<meta name="viewport" content="width=device-width">
<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
<link href='http://fonts.googleapis.com/css?family=Carrois+Gothic+SC' rel='stylesheet' type='text/css'>

<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.0/css/font-awesome.css">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
<script type="text/javascript" src="https://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.js"></script>
<link rel="stylesheet" href=<?php echo site_url('manual.css')?>>
</head>

<header>
  <nav class="panel collapse navbar-collapse">
    <ul class="nav nav-pills navbar-right ">
      <li>
       <form class="navbar-form " method = "post" action=<?php echo site_url("search");?>>
        <div class="btn-group">
          <a class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" href="#">Select A Game <span class="caret"></span>  </a>
          <ul class="dropdown-menu">
            <?php foreach ($supported_games as $key => $game):?>
              <li>
                <a href=<?php $url = url_title($game['product_game'],'_'); echo site_url("search/$url")?>> <?php echo $game['product_game'] ?></a>
              </li>
            <?php endforeach ?>
          </ul>
        </div>
      </form>  
      </li>
      <li>
        <h4 class="molot"><?php if($user_id > 0) echo $user_name ?></h4>
      </li>
      <li>
        <?php  echo (  $user_id == 0 ? anchor('auth', "Login") : anchor('auth/logout', "Logout") )?>
      </li>
      <li>
        <a href= <?php echo site_url('cart')?> ><i class="fa fa-shopping-cart"></i><span class="badge"><?php echo $num_items ?></span></a>
      </li>
    </ul>
    <ul class="nav nav-pills navbar-left">
      <a href= <?php echo site_url('') ?> ><h4 class='molot'>Psycho Store</h4></a>
    </ul>
  </nav>
  <div class="container">
    <div class="row">
      <div class="col-xs-12 text-center">
        <img class="img-responsive" src="http://cdn.shopify.com/s/files/1/0063/2872/t/4/assets/logo.png?2093" style="display: inline">
      </div>
    </div>
  </div>
</header>
<body>
  <div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=601282446622582";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
</body>




