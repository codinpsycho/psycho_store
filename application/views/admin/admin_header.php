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
<link rel="icon" type="image/jpg" href=<?php echo $favico ?> >

<link href='http://fonts.googleapis.com/css?family=Carrois+Gothic+SC' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href=<?php echo site_url('css/bootstrap.min.css') ?> >
<link rel="stylesheet" href= <?php echo site_url('css/font-awesome.min.css') ?> >
<link rel="stylesheet" href=<?php echo site_url('css/manual.css')?>>
<script type="text/javascript" src= <?php echo site_url('scripts/jquery-2.1.3.min.js') ?> ></script>
<script type="text/javascript" src= <?php echo site_url('scripts/bootstrap.min.js') ?> ></script>

</head>

<header>
  <nav class="panel collapse navbar-collapse">
    <ul class="nav nav-pills navbar-right ">
      <li>
    	<a class='navbar-btn' href = <?php echo site_url('admin/orders') ?> >Orders</i></a>
      </li>
      <li>
    	<a class='navbar-btn' href = <?php echo site_url('admin/products') ?> >Products</i></a>
      </li>
      <li>
      <a class='navbar-btn' href = <?php echo site_url('admin/users') ?> >Users</i></a>
      </li>      
      <li>
        <div class="btn-group">
          <a class="btn navbar-btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" href="#">Other <span class="caret"></span>  </a>
            <ul class="dropdown-menu">
                <li>
                  <a class='navbar-btn' href = <?php echo site_url('admin/feedback') ?> >Feedback</a>
                  <a class='navbar-btn' href = <?php echo site_url('admin/mails') ?> >Emails</a>
                  <a class='navbar-btn' href = <?php echo site_url('admin/checkouts') ?> >Checkouts</a>
                  <a class='navbar-btn' href = <?php echo site_url('admin/logistics') ?> >Logistics</a>
                  <a class='navbar-btn' href = <?php echo site_url('admin/discounts') ?> >Discounts</a>
                </li>
            </ul>          
        </div>
      </li>
      <li>
      	<?php if($user_id > 0): ?>
        	<h4 class="navbar-text"> <?php echo $user_name ?> </h4>
    	<?php endif; ?>
      </li>
      <li>
        <?php $redirect_url = rawurlencode(uri_string()); ?>
      	<?php  if ( $user_id == 0 ): ?> <a href= <?php  echo site_url('auth/login').'?redirect_url='.$redirect_url; ?> > <h5 class="navbar-btn">Login </h5></a>
      	<?php else: ?> <a href= <?php echo site_url('auth/logout').'?redirect_url='.$redirect_url; ?> > <h5 class="navbar-btn">Logout </h5></a> <?php endif; ?>
      </li>
    </ul>
    <ul class="nav nav-pills navbar-left">    	
		<a href= <?php echo site_url('') ?> ><h4 class='molot navbar-text'>Psycho Store</h4></a>    
    </ul>
    <ul class="nav nav-pills">
      <form class='navbar-form' method = "post" action = "<?php echo site_url("admin/search")?>" role="form">
        <select class="form-control navbar-btn" name="search_option">
          <option value='orders'>Orders</option>
          <option value ='products'>Products</option>
          <option value='users'>Users</option>
        </select>
        <div class="form-group">
          <input type="text" class="form-control" name="search_query" placeholder="Search">
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
      </form>
    </ul>
  </nav>
  
</header>

<body>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
</body>





