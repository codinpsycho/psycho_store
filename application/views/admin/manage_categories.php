<div class="container top-bottom-space">  
    <h1> Manage Categories</h1>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <span class='pull-right'>
                <a class="btn btn-primary play" href= <?php echo site_url('admin/add_category') ?> >Add New Category</a>
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
                            <th>id</th>
                            <th>name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($categories as $key => $value) {

                           ?>
                           <tr>
                            <td> <?=$value['id']?> </td>
                            <td> <?=$value['name']?> </td>
                            <td><a href="<?=base_url().'admin/edit_category/'.$value['id']?>"> <i class="fa fa-pencil"></i> Edit</a></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>


        </div>
    </div>
</div>
</div>
