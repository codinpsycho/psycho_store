<title><?php echo $title ?></title>
<meta property="fb:app_id" content="601282446622582" />
<meta property="fb:admins" content="100001096628321"/>
<meta property="og:title" content= "<?php echo $title ?>" />
<meta property="og:type" content="website"/>
<meta property="og:url" content=<?php echo $url ?> />
<meta property="og:image" content=<?php echo $image ?> />
<meta property="og:description" content="<?php echo $description ?>" />
<meta name="viewport" content="width=device-width">
<?php echo meta('description', $description) ?>
<?php echo meta('keywords', $keywords) ?>
<link rel="icon" type="image/png" href=<?php echo $favico ?> >

<link href='http://fonts.googleapis.com/css?family=Carrois+Gothic+SC' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href=<?php echo site_url('css/bootstrap.min.css') ?> >
<link rel="stylesheet" href= <?php echo site_url('css/font-awesome.min.css') ?> >
<link rel="stylesheet" href=<?php echo site_url('css/manual.css?v3')?>>
<script type="text/javascript" src= <?php echo site_url('scripts/jquery-2.1.3.min.js') ?> ></script>
<script type="text/javascript" src= <?php echo site_url('scripts/bootstrap.min.js') ?> ></script>

<header>
  <nav class="panel navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand molot" href= <?php echo site_url('') ?> >Psycho Store</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav navbar-right">
        <li class="nav-pills">
          <a target="_blank" href= <?php echo site_url("explore/gaming-anime-geek-t-shirts-india") ?> >Tees</i></a>
        </li>
        <li class="nav-pills">
          <a target="_blank" href= <?php echo site_url("explore/gaming-anime-geek-mobile-covers-india") ?> >Mobile Covers</i></a>
        </li>
        <li class="nav-pills">
          <a target="_blank" href= <?php echo site_url("explore/gaming-anime-geek-coffee-mugs-india") ?> >Mugs</i></a>
        </li>       
        <li class="dropdown">
          <form class="navbar-form" role="search" method = "post" action=<?php echo site_url("like");?>>
          <div class="btn-group">
            <a class=" molot btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" href="#">What do you like <span class="caret"></span>  </a>
            <ul class="dropdown-menu">
              <?php foreach ($supported_games as $key => $game):?>
                <li>
                  <a href=<?php $game_url = url_title($game['product_game'], '-', true); echo site_url("like/$game_url")?>> <?php echo $game['product_game'] ?></a>
                </li>
              <?php endforeach ?>
            </ul>
          </div>
        </form>
        </li>
        <li>
          <?php if($user_id > 0): ?>
            <h4 class="navbar-text"> <?php echo $user_name ?> </h4>
          <?php endif; ?>
        </li>
        <li>
          <?php $redirect_url = rawurlencode(uri_string()); ?>
          <?php  if ( $user_id == 0 ): ?> <a href= <?php  echo site_url('auth/login').'?redirect_url='.$redirect_url; ?> > Login </a>
          <?php else: ?> <a href= <?php echo site_url('auth/logout').'?redirect_url='.$redirect_url; ?> >Logout</a> <?php endif; ?>
        </li>
        <li>
          <a class="" href= <?php echo site_url('cart')?> ><i class="fa fa-shopping-cart"></i><span class="badge"><?php echo $num_items ?></span></a>
        </li>
      </ul>      
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
</header>