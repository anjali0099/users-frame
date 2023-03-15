
<div class="card" style="background-color: #c3cdcc;">
    <div class="card-body">
        <h5>Add/Update User Page</h5>
    </div>
</div>

<div class="container" style="margin-top: 20px;">
    <form id='add-user-form' method="post" action="<?=base_url()?>Login/addUpdateUser">

        <div class="alert alert-danger" role="alert" id="alert-message" style="display: none;">
            Please enter the data in all the following fields!!!
        </div>
        <?php if(isset($edit)):?>
            <input type="hidden" name="id" value="<?php echo $edit[0]['id'];?>" />
        <?php endif;?>
        <div class="form-group">
            <label >Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Enter Username"  value=" <?php echo isset($edit) ? ($edit[0]['username']) :  null;?>" required />

        </div>
        <div class="form-group">
            <label >Password</label>
            <input type="text" class="form-control" id="password" name="password" placeholder="Enter Password" required value=" <?php echo isset($edit) ? ($edit[0]['password']) :  '';?>">
        </div>
        <div class="form-group">
            <label >Address</label>
            <input type="text" class="form-control" id="address" name="address" placeholder="Enter Address" required value=" <?php echo isset($edit) ? ($edit[0]['address']) :  null;?>">

        </div>

        <div id="buttons" style="float: right">
            <?php if(isset($edit)){?>
                <button type="submit" class="btn btn-info">Update</button>
            <?php } else {?>
                <button type="submit" class="btn btn-primary">Add</button>
            <?php }?>
            <a href="<?=base_url()?>" class="btn btn-warning">Cancel</a>
        </div>

    </form>
</div>

<script>
    $("#add-user-form").validate();

    $(function () {

        $('#add-user-form').submit(function () {
            if($(this).valid()) {

            }else
            {
                $('#alert-message').css("display", "block");

                setTimeout(function() {
                    $("#alert-message").hide('blind', {}, 500)
                }, 2000);
            }
        });
    });

   /* $("#add-user-form").validate({
        rules: {
            username: {
                required:true,
                rangelenght: [4,20]
            }
        },
        messages:{
            username:{
                required:"Please enter username with minimum 4 letters"
            }
        }
    })*/
</script>
