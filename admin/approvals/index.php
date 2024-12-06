
<div class="card card-outline rounded-0 card-maroon">
    <div class="card-header">
        <h5 class="card-title"><b><i>Approval Tasks</i></b></h5>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <table class="table table-bordered table-striped" id="approvals-table" style="text-align:center;width:100%;">
                <thead>
                    <tr>
                        <th>Approval ID</th>
                        <th>Request ID</th>
                        <th>Approval Order</th>
                        <th>Role</th>
                        <th>Description</th>
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

                    // Fetch the approver's tasks from the view
                    //$current_user_id = $_SESSION['user_id']; // Assume logged-in user's ID is stored in session
                    $current_user_id = '10093';
                    $qry = odbc_exec($conn2, "
                       SELECT 
                            approval_id,
                            request_id,
                            approval_order,
                            approver_role,
                            request_description,
                            approval_status
                        FROM view_sequential_approvals
                        WHERE approver_id = $current_user_id
                        ORDER BY approval_status ASC, approval_order ASC
                    ");

                    // Loop through the query results
                    while ($row = odbc_fetch_array($qry)):
                        // Badge color for approval status
                        $status_badge = "badge-secondary";
                        if ($row['approval_status'] == "pending") $status_badge = "badge-warning";
                        elseif ($row['approval_status'] == "approved") $status_badge = "badge-success";
                        elseif ($row['approval_status'] == "rejected") $status_badge = "badge-danger";
                    ?>
                    <tr>
                        <td><?php echo $row['approval_id'] ?></td>
                        <td><?php echo $row['request_id'] ?></td>
                        <td><?php echo $row['approval_order'] ?></td>
                        <td><?php echo $row['approver_role'] ?></td>
                        <td><?php echo $row['request_description'] ?></td>
                        <td>
                            <span class="badge <?php echo $status_badge ?>"><?php echo ucfirst($row['approval_status']) ?></span>
                        </td>
                        <td align="center">
                            <?php if ($row['approval_status'] == "pending"): ?>
                                <button class="btn btn-success btn-sm approve-btn" data-approval-id="<?php echo $row['approval_id'] ?>">
                                    Approve
                                </button>
                                <button class="btn btn-danger btn-sm reject-btn" data-approval-id="<?php echo $row['approval_id'] ?>">
                                    Reject
                                </button>
                            <?php else: ?>
                                <span class="text-muted">No Actions</span>
                            <?php endif; ?>
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
