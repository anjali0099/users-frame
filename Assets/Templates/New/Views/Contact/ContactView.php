<style type="text/css">
.form-control.error {
	border-color: red;

} 
.form.error{
	color: darkred;
}
.toast.success {
	background-color: #92ef92;
}
.toast.error {
	background-color: red;
}

</style>

<?php if(isset($_SESSION['Error']) && $_SESSION['Error']!=''){?>
  <div class="container mt-2">
    <div role="alert" aria-live="assertive" aria-atomic="true" class="toast msg-alert error fade show" data-autohide="true" data-animation="true" data-delay="500">
      <div class="toast-body">
        <?=$_SESSION['Error'];?>
      </div>
    </div>
  </div>
<?php $_SESSION['Error']='';}?>

<?php if(isset($_SESSION['Success']) && $_SESSION['Success']!=''){?>
    <div class="container mt-2">
      <div role="alert" aria-live="assertive" aria-atomic="true" class="toast success msg-alert fade show" data-autohide="true" data-animation="true" data-delay="500">
        <div class="toast-body">
          <?=$_SESSION['Success'];?>
        </div>
      </div>
    </div>
<?php $_SESSION['Success']='';}?>


<div class="container mt-5">
    <div class="row">
        <div class="col">
            <div class="card">
              
              <div class="col-md-6 mt-2">
              <form action='<?=base_url()?>Contact/contact_upload' method="post" enctype="multipart/form-data">
                    <input type="file" name="file" />
                    <input type="submit" class="btn btn-sm btn-success" name="importSubmit" value="IMPORT">
              </form>
              </div>
                <div class="col-md-6 mt-2">
                  <button type="button" id="contactmodalid" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#contactmodal">
                    Add Contact
                  </button>
                </div>

                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="text-align: center;">Phone Number</th>
                                <th style="text-align: center;">Email</th>
                                <th style="text-align: center;">Address</th>
                                <th colspan="2" style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                              if (!empty($contact)) {
                                  foreach ($contact as $key => $value) { 
                                    ?>
                                    <tr>
                                      <td><?=$value['phone']?></td>
                                      <td><?=$value['email']?></td>
                                      <td><?=$value['address']?></td>
                                      <td>
                                        <a data-toggle="modal" data-target="#contactmodal" onclick="return populate_edit_contact(
                                          '<?=$value['contact_id']?>',
                                          '<?=$value['phone']?>',
                                          '<?=$value['email']?>',
                                          '<?=$value['address']?>'
                                          )" class="edit_contact_<?=$value['contact_id']?>">
                                          <button class="btn btn-sm btn-primary">Edit</button>
                                        </a>
                                      </td>
                                      <td>
                                        <button class="btn btn-sm btn-danger" onclick="delete_contact('<?=$value['contact_id']?>')">Delete</button>
                                      </td>
										                </tr>
                            <?php   }
                              }	
                              else
                              {
                                echo '<p>No record found</p>'; 
                              }
                              ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Contact Modal -->
<div class="modal fade" id="contactmodal" tabindex="-1" role="dialog" aria-labelledby="contactmodalTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="contactmodal_title">Contact</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
              <form id="contactmodal_form" method="post" enctype="multipart/form-data">
                <input type='hidden' name='editid' id='editid' class='editid'>
                <div class="form-group">
                  <label>Phone Number</label>
                  <input required type="tel" class="form-control" id="phone" name="phone">
                </div>	
                <div class="form-group">
                  <label>Email address</label>
                  <input required type="email" class="form-control" id="email" name="email">
                </div>
                  <div class="form-group">
                  <label>Address</label>
                  <input required type="text" class="form-control" id="address" name="address">
                </div>
                  
                <div class="modal-footer">
                  <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" id="add_contact" class="btn btn-sm btn-primary">Submit</button>
                  <button type="submit" id="edit_contact" class="btn btn-sm btn-primary">Update</button>
                </div>
              </form>
        </div>
    </div>
  </div>
</div>