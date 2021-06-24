<div class="container top-bottom-space">  
    <h1> Manage SubCategories</h1>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <span class='pull-right'>
                <a class="btn btn-primary play" href= <?php echo site_url('admin/add_subcategory') ?> >Add New SubCategory</a>
            </span>
        </div>
    </div>    
    <hr>
    <div class="well">
        <div class="row ">
            <div class="col-md-12">


                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th>Id#</th>
                            <th>Game Name</th>
                            <th>Category</th>
                            <th>Image</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($subcategories as $key => $value) {

                           ?>
                           <tr>
                            <td> <?=$value['id']?> </td>
                            <td> <?=$value['game_name']?> </td>
                            <td> <?=$value['category']?> </td>
                            <td> 
                                <?php if(!empty($value['file_path'])) { ?>
                                <a href="<?=base_url().$value['file_path']?>" target="_blank"><img src="<?=base_url().$value['file_path']?>" width=50px height=50px></a>
                                <?php } ?> 
                            </td>
                            <td><a href="<?=base_url().'admin/edit_subcategory/'.$value['id']?>"> <i class="fa fa-pencil"></i> Edit</a></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>


        </div>
    </div>
</div>
</div>
