<div class="container top-bottom-space">  
    <h1> Manage Product Types</h1>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <span class='pull-right'>
                <a class="btn btn-primary play" href= <?php echo site_url('admin/update_product_types') ?> >Update Product Types</a>
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
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($types as $key => $value) {

                           ?>
                           <tr>
                            <td> <?=$key+1?> </td>
                            <td> <?=$value?> </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>


        </div>
    </div>
</div>
</div>
