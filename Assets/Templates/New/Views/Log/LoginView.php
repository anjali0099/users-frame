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

  <div class="container">
    <div class="card mt-4">
        <div class="card-body">

          <form method="POST" action='<?=base_url()?>Log/login' id="login_form">
            <div class="form-group">
              <label for="exampleInputEmail1">Email address</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email address" required>
              <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
            </div>
            <div class="form-group">
              <label for="exampleInputPassword1">Password</label>
              <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary">Login</button>
            </div>
            <div class="form-group">
              <p>Donot have an account?..
                <button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#registermodal">
                  Register
                </button>
              </p>
            </div>

            </form> 

          </div>
    </div>
  </div>



      <!-- register modal -->
      <div class="modal fade" id="registermodal" tabindex="-1" aria-labelledby="registermodal" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="register_modal">You can register here...</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form id="register_form"  method="post" enctype="multipart/form-data">
                <div class="form-group">
                  <label>Firstname</label>
                  <input required type="text" class="form-control" id="fname" name="firstname" placeholder="Enter First Name" >
                </div>
                <div class="form-group">
                  <label>Lastname</label>
                  <input required type="text" class="form-control" id="lname" name="lastname" placeholder="Enter Last Name" >
                </div>
                <div class="form-group">
                  <label>Email address</label>
                  <input required type="email" class="form-control" id="emailaddress" name="email" placeholder="name@example.com" >
                </div>
                <div class="form-group">
                  <label>Address</label>
                  <input required type="text" class="form-control" id="address" name="address" placeholder="Enter Address" >
                </div>
                <div class="form-group">
                  <label>Password</label>
                  <input required type="password" class="form-control" id="pass" name="password" placeholder="Password" >
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Register</button>

              </div>
            </form>

          </div>
        </div>
      </div>