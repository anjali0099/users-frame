
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
			<div class="card">
				<div class="container">
					<div class="row">
						<div class="col-md-6 mb-4 mt-4">
							<button type="button" class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#changepassmodal">
								Change Password
							</button>
					
							<a href="User/user_info"><button type="button" class="btn btn-sm btn-primary">
								User Info
							</button></a>
							<a href="Contact"><button type="button" class="btn btn-sm btn-info">
								Contact
							</button></a>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-6 mb-4 mt-4">
							<button type="button" id="createmodalid" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#createmodal">
								Add new users
							</button>
							<a href="User/user_log"><button type="button" id="userlog" class="btn btn-sm btn-primary" > View User Log
							</button></a>
						</div>
						<div class="col-md-6 mt-2">
							<div class="row">
								<div class="form-group mb-4 mt-4">
									<select name="state" id="maxRows" class="form-control" style="width: 150px;">
										<option value="500">Show All</option>
										<option value="5">5</option>
										<option value="10">10</option>
										<option value="15">15</option>
										<option value="20">20</option>
										<option value="50">50</option>
										<option value="75">75</option>
										<option value="100">100</option>
									</select>
								</div>
								<div class="form-group mb-4 mt-4 col-md-8">
									<input type="text" name="search" id="search" placeholder="Search" class="form-control" />
									<div id="result"></div>
								</div>
							</div>
						</div>
					</div>	
					
				</div>


				<div class="card-body">
					<form method='post' action='<?=base_url()?>User/checkbox_del'>
						<input type='submit' class="btn btn-sm btn-danger" value='Delete' id="deleteallbtn" name='btndelete' disabled>
						<a href='<?=base_url()?>User/export_csv'><button type = 'button' class="btn btn-sm btn-primary">Download CSV File</button></a>
						<a href='<?=base_url()?>User/export_xls'><button type = 'button' class="btn btn-sm btn-success">Download Excel File</button></a><br><br>
						<table  id="mytable" class="table table-bordered table-dark table-responsive">
							<tbody>
								<tr>
									<!-- <th>ID</th> -->
									<th><input type='checkbox' class="selectdel" id="selectall" name='selectall'  value='' ></th>
									<th style="text-align: center;">Firstname</th>
									<th style="text-align: center;">Lastname</th>
									<th style="text-align: center;">Company Name</th>
									<th style="text-align: center;">Company Address</th>
									<th colspan="4" style="text-align: center;">Action</th>
								</tr>
								<?php if (!empty($user)) {
									foreach ($user as $key => $value) { 
										?>
										<tr>
											<td><input type='checkbox' class="selectdel" name='delete[]' value='<?= $value['userId']?>' ></td>
											
											<td><?=$value['firstname']?></td>
											<td><?=$value['lastname']?></td>
											<td><?=$value['companyname']?></td>
											<td><?=$value['companyaddress']?></td>
											<td>
												<a data-toggle="modal" data-target="#createmodal" onclick="return populate_edit(
													'<?=$value['userId']?>',
													'<?=$value['firstname']?>',
													'<?=$value['lastname']?>',
													'<?=$value['email']?>',
													'<?=$value['address']?>',									
													'<?=$value['companyname']?>',
													'<?=$value['companyaddress']?>',
													)" class="edit_profile_<?=$value['userId']?>">
													<button class="btn btn-sm btn-info">Edit</button>
												</a>&emsp;&emsp;
											</td>
											<td>
											<a href='<?=base_url()?>User/view_log?user_id=<?=$value['userId']?>'><button type='button' class="viewlog btn btn-sm btn-success">View Log</button></a>
											</td>
											<td>
												<button type='button' class="btn btn-sm btn-primary" data-toggle="modal" data-target="#changepassmodal" onclick="change_pass('<?=$value['userId']?>')" >Change Password</button>
											</td>
											<td>
												<button class="btn btn-sm btn-danger" onclick="delete_user('<?=$value['userId']?>')">Delete</button>
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
					</form>
					<div class="container">
						<nav aria-label="Page navigation example">
							<ul class="pagination"></ul>
						</nav>
					</div>
				</div>

			</div>
		</div>




		<!-- create modal -->
		<div class="modal fade" id="createmodal" tabindex="-1" aria-labelledby="createmodal" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="create_modal">Modal title</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form id="createmodal_form" method="post" enctype="multipart/form-data">
							<input type='hidden' name='editid' id='editid' class='editid'>    

							<div class="form-group">
								<label>Firstname</label>
								<input required type="text" class="form-control" id="firstname" name="firstname" placeholder="Enter First Name" >
							</div>
							<div class="form-group">
								<label>Lastname</label>
								<input required type="text" class="form-control" id="lastname" name="lastname" placeholder="Enter Last Name" >
							</div>
							<div class="form-group">
								<label>Email address</label>
								<input required type="email" class="form-control" id="email" name="email" placeholder="name@example.com" >
							</div>
							<div class="form-group">
								<label>Address</label>
								<input required type="text" class="form-control" id="address" name="address" placeholder="Enter Address" >
							</div>
							<div class="form-group" id="hidepass">
								<label>Password</label>
								<input required type="password" class="form-control" id="password" name="password" placeholder="Password" >
							</div>
							<div class="form-group">
								<label>Company Name</label>
								<input required type="text" class="form-control" id="companyname" name="companyname" placeholder="Enter Company Name" >
							</div>
							<div class="form-group">
								<label>Company Address</label>
								<input required type="text" class="form-control" id="companyaddress" name="companyaddress" placeholder="Enter Company Address" >
							</div>
							
							<div class="modal-footer">
								<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
								<button type="submit" id="adduser" class="btn btn-sm btn-primary user_submit_form">Submit</button>
								<button type="submit" id="edituser" class="btn btn-sm btn-primary user_submit_form">Update Data</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>


<!-- changepassword modal -->
<div class="modal fade" id="changepassmodal" tabindex="-1" aria-labelledby="changepassmodal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="changepass_modal">Change Password</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      	<div class="modal-body">
			<form id="changepassmodal_form" method="post">
				<div class="form-group">
					<label>Old Password</label>
					<input type="password" class="form-control" id="oldpassword" name="oldpassword">
				</div>
				<div class="form-group">
					<label>New Password</label>
					<input type="password" class="form-control" id="newpassword" name="newpassword">
				</div>
				<div class="form-group">
					<label>Confirm Password</label>
					<input type="password" class="form-control" id="cpassword" name="cpassword">
				</div>
				
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" id='loguserpass' class="btn btn-primary">Change Password</button>
					<button type="submit" id='userpass' class="btn btn-primary">Update Password</button>
				</div>
			</form>
      	</div>
    </div>
  </div>
</div>

<script>
//search
$(document).ready(function () {

function load_data(query) {
	$.ajax({
		url: "User/search",
		method: "POST",
		data: { 'query': query },

		success: function (data) {
			console.log('ok');
			$('.main-content').html(data);
			$('#search').val(query);
			$('#search').focus();
		}
	});
}
$('#search').keyup(function () {
	var search = $(this).val();
	load_data(search);
});
});

</script>


