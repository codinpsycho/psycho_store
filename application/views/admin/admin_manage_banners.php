<div class="container top-bottom-space">  
    <h1> Manage Banners</h1>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <span class='pull-right'>
                <a class="btn btn-primary play" href= <?php echo site_url('admin/add_banner') ?> >Add New Banner</a>
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
                            <th>Banner</th>
                            <th>Page Link</th>
                            <th>Sort</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($banners as $key => $value) {

                           ?>
                           <tr>
                            <td> <?=$value['id']?> </td>
                            <td> 
                                <a href="<?=base_url().$value['file_path']?>" target="_blank"><img src="<?=base_url().$value['file_path']?>" width=50px height=50px></a> 
                            </td>
                            <td> <?=$value['page_url']?> </td>
                            <td> <?=$value['sort']?> </td>
                            <td> <?=$value['is_active']?> </td>
                            <td>
                                <a href="<?=base_url().'admin/edit_banner/'.$value['id']?>"> <i class="fa fa-pencil"></i> Edit</a>
                                <a href="<?=base_url().'admin/delete_banner/'.$value['id']?>" onclick="return confirm('Are you sure?')"> <i class="fa fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>


        </div>
    </div>
</div>
</div>
