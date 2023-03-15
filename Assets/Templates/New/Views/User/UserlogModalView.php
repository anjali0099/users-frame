
    <div class="modal-content">
		<div class="modal-header">
        <h5 class="modal-title" id="logview">User Log</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

		
      <div class="modal-body">
        <table  id="mytable" class="table table-bordered table-white">
          <tbody>
            <tr>
              <th>Login Time</th>
              <th>Logout Time</th>
            </tr>
            <?php
			      if (isset($array) && !empty($array)) {
              foreach ($array as $key => $value) { 
              ?>
                <tr>				
                  <td><?php echo $value["logintime"]?></td>
                  <td><?php echo $value["logouttime"]?></td>
                </tr>
              <?php }
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