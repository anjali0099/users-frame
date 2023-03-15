<div class="container mt-5">
	<div class="card">
		<div class="card-body">
        	<table  id="mytable" class="table table-bordered table-dark">
				<tbody>
					<tr>
						<th>Name</th>
						<th>Email</th>
					</tr>
					<?php  if (isset($info) && !empty($info)) {
						?>
                        <tr>				
							<td><?php echo $info["name"]?></td>
							<td><?php echo $info["email"]?></td>
							</tr>
						<?php 
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