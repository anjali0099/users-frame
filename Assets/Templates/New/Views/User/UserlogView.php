<div class="container mt-5">
	<div class="card">
		<div class="card-body">
        	<table  id="mytable" class="table table-bordered table-dark">
				<tbody>
					<tr>
						<th>First Name</th>
						<th>Login Time</th>
						<th>Logout Time</th>
						<th>Date</th>
						<th>Total Login</th>
					</tr>
					<?php if (isset($all_log) && !empty($all_log)) {
						foreach ($all_log as $key => $value) { 
						?>
						<tr>				
							<td><?=$value['firstname']?></td>
							<td><?=$value['logintime']?></td>
							<td><?=$value['logouttime']?></td>
							<td><?=$value['date']?></td>
							<td><a id="logmodal" data-toggle="modal" data-target="#logviewmodal"><?=$value['totallogin']?></a></td>
						</tr>
						<?php   }
					}	
					elseif (isset($single_log) && !empty($single_log))
					{
						foreach ($single_log as $key => $value) {
							//  echo"<pre>"; print_r($value); 
						?>
						<tr>								
						<td><?=$value['firstname']?></td>
						<td><?=$value['logintime']?></td>
						<td><?=$value['logouttime']?></td>
						<td><?=$value['date']?></td>
						<td><a href="" data-id="<?=$value['userId']?>"  onclick="viewlog('<?=$value['userId']?>','<?=$value['date']?>')" data-toggle="modal" data-target="#logviewmodal" ><?=$value['totallogin']?></a></td>
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
            <div class="col-md-3">
				<button class="btn btn-sm btn-primary" onclick="history.go(-1);">Back </button>
			</div>
        </div>    
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="logviewmodal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="logviewmodal" aria-hidden="true">
	<div class="modal-dialog" id="log_modal">
		
	</div>
</div>



<script>
	function viewlog(userId,date)
	{
		$.ajax({
			type: "post",
			url: 'total_login',
			data: { 
			'userId': userId,
			'date': date
		 	},
			success: function (data) {

				$('#log_modal').html(data);
				// alert(data);
				// location.reload(true);
			}
		});
	}
</script>