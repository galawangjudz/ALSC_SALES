<div class="card" id="container">
    <div class="navbar-menu">
        <a href="<?php echo base_url ?>admin/?page=requests/pending" class="main_menu" id="pending-link" style="border-left:solid 3px white;">
            <i class="nav-icon fas fa-clock"></i>&nbsp;&nbsp;&nbsp;Pending Requests
        </a>
        <a href="<?php echo base_url ?>admin/?page=requests/approved" class="main_menu" id="approved-link">
            <i class="nav-icon fas fa-check-circle"></i>&nbsp;&nbsp;&nbsp;Approved Requests
        </a>
        <a href="<?php echo base_url ?>admin/?page=requests/rejected" class="main_menu" id="rejected-link">
            <i class="nav-icon fas fa-times-circle"></i>&nbsp;&nbsp;&nbsp;Rejected Requests
        </a>
    </div>
</div>

<div class="card card-outline rounded-0 card-maroon">
    <div class="card-header">
        <h5 class="card-title"><b><i>Requests List</i></b></h5>
        <div class="card-tools">
            <a class="btn btn-primary btn-flat border-primary new_request" href="javascript:void(0)" style="font-size:14px;">
                <i class="fa fa-plus"></i>&nbsp;&nbsp;Add New Request
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <table class="table table-bordered table-striped" id="data-table" style="text-align:center;width:100%;">
                <thead>
                <tr>
                        <th>Request ID</th>
                        <th>Request Type</th>
                        <th>Requester</th>
                        <th>Description</th>
                        <th>Date Submitted</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // ODBC Connection
                    
                    if (!$conn2) {
                        echo "Connection failed: " . odbc_errormsg();
                        exit;
                    }

                    // Query to fetch requests
                    $qry = odbc_exec($conn2, "
                        SELECT 
                            r.id AS request_id, 
                            r.request_type, 
                            r.requester_id, 
                            r.description, 
                            r.created_at, 
                            r.status 
                        FROM requests r
                        ORDER BY r.created_at DESC
                    ");

                    // Loop through the query results
                    while ($row = odbc_fetch_array($qry)):
                        $requester_id = $row['requester_id'];

                        // Fetch requester name (if available)
                        $requester_query = odbc_exec($conn2, "SELECT c_realname FROM t_car_users WHERE c_employee_code = '$requester_id'");
                        $requester_row = odbc_fetch_array($requester_query);
                        $requester_name = $requester_row ? $requester_row['c_realname'] : "Unknown";

                        // Status badge colors
                        $status_badge = "badge-secondary";
                        if ($row['status'] == "pending") $status_badge = "badge-warning";
                        elseif ($row['status'] == "approved") $status_badge = "badge-success";
                        elseif ($row['status'] == "rejected") $status_badge = "badge-danger";
                    ?>
                    <tr>
                        <td><?php echo $row['request_id'] ?></td>
                        <td><?php echo $row['request_type'] ?></td>
                        <td><?php echo $requester_name ?></td>
                        <td><?php echo $row['description'] ?></td>
                        <td><?php echo date('Y-m-d H:i:s', strtotime($row['created_at'])) ?></td>
                        <td>
                            <span class="badge <?php echo $status_badge ?>"><?php echo ucfirst($row['status']) ?></span>
                        </td>
                        <td align="center">
                            <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                Action
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu" role="menu">
                                <a class="dropdown-item view-request" data-request-id="<?php echo $row['request_id'] ?>">
                                    <span class="fa fa-eye text-info"></span> View
                                </a>
                                <?php if ($row['status'] == "pending"): ?>
                                    <a class="dropdown-item edit-request" data-request-id="<?php echo $row['request_id'] ?>">
                                        <span class="fa fa-edit text-primary"></span> Edit
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item delete-request" data-request-id="<?php echo $row['request_id'] ?>">
                                        <span class="fa fa-trash text-danger"></span> Delete
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// Close the ODBC connection
odbc_close($conn2);
?>
