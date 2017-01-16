<div class="container top-bottom-space">  
    <h1> Add/Edit Products
        <span class="pull-right navbar-text"> <small> <?php echo $num_prods?> Product(s) </small> </span>
    </h1>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <form class="form-inline well" method="post" action= <?php echo site_url('admin/products') ?> >
                <div class="form-group">
                    <label>Sort By </label>
                    <select class="form-control" name="sort">
                        <option value="latest"> Latest </option>
                        <option value="popular"> Popularity </option>
                    </select>
                    <label>Type </label>
                    <select class="form-control" name="type">
                        <option value="all"> All </option>
                        <option value="tshirt"> Tshirt </option>
                        <option value="hoodie"> Hoodie </option>
                        <option value="mugs"> Mugs </option>
                        <option value="mobilecover"> Mobile Cover </option>                        
                    </select>
                    <label>Like </label>
                    <select class="form-control" name="game">
                        <option value="all"> All </option>
                        <?php foreach ($supported_games as $game):?>
                          <option>
                            <?php echo $game['product_game'] ?>
                          </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn btn-primary" type="submit" > Sort </button>
                <span class='pull-right'>
                    <a class="btn btn-primary play" href= <?php echo site_url('admin/add_product') ?> >Add New Product</a>
                </span>
            </form>
        </div>
    </div>    
    <hr>
    <div class="well">
    	<div class="row ">
	    	<div class="col-md-12">
	    		<?php echo $products_table; ?>
			</div>
		</div>
	</div>
</div>
