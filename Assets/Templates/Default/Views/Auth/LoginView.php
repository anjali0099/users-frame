<?php
   
    $_SESSION['ok'] = $filterPage;

   
?>
<div class="container">

    <div>
        <div style="margin-top:10px;">
            <form id="changeLimit" action="<?=base_url()?>" method="POST">
                <label>View</label>

                <select id="pageSize" name="pageSize">
                    <option <?php echo($filterPage=='5') ? "selected " : "" ?> value="5">5</option>
                    <option <?php echo($filterPage=='10') ? "selected " : "" ?> value="10">10</option>
                    <option <?php echo($filterPage=='20') ? "selected " : "" ?> value="20">20</option>
                    <option <?php echo($filterPage=='All') ? "selected " : "" ?> value="All">All</option>
                </select>

                <label>records:</label>

            </form>
        </div>

        <div id="add-button">
            <a href="<?= base_url() ?>Login/addEditUserForm" id="add-user" class="btn btn-primary">Add new user</a>
        </div>
        <div>

            <table class="table table-bordered" id="user-list-table">
                <caption>List of users</caption>
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Username</th>
                        <th scope="col">Password</th>
                        <th scope="col">Address</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <th scope="row"><?php echo $user['id']; ?></th>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['password']; ?></td>
                        <td><?php echo $user['address']; ?></td>
                        <td>
                            <a id="edit-user" href="<?= base_url() ?>Login/addEditUserForm?id=<?= $user['id']; ?>"
                                class="btn btn-info">Edit</a>
                            <a type="submit" onClick="return confirm('Are you sure you want to delete?')"
                                href="<?= base_url() ?>Login/Delete?id=<?= $user['id']; ?>" id="delete-user"
                                class="btn btn-danger">Delete</a>
                        </td>

                    </tr>
                    <?php endforeach; ?>

                    <!--showing no records found message on table if there is no data on table -->
                    <?php $paginate->noRecords(); ?>
                </tbody>
            </table>

        </div>

        <!--this will create pagination if there is more number of records than limit -->
        <?php $paginate->createLinks();  ?>

        <script>
        $("#add-user-form").validate();


        $("#pageSize").change(function() {

            $('#changeLimit').submit();


        });







        //$(document).ready(function () {
        // var data1 = "<?php echo count($users);?>"
        //if (data1 == "0") {
        // debugger;
        //$('#user-list-table tbody tr td').append('<tr>No records found again</tr>');
        // $('#user-list-table tbodY').append('<tr><td>No records</td?</tr>')
        // }
        // });
        /*  $( document ).ready(function() {
              $("#user-list-table").DataTable({
                  pageLength : 5,
                  searching: false,
                  info: false,
              });
          });*/
        </script>