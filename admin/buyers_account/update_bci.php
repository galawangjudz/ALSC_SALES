<?php
require_once('../../config.php');

$acc = $_GET['acc'];

if (isset($_GET['acc'])) {
    $sql = "SELECT * FROM t_buyers_account WHERE c_account_no = ?";
    $acc = $_GET['acc'];
  

    $qry = odbc_prepare($conn2, $sql);
    if (!$qry) {
        die("Preparation of the statement failed: " . odbc_errormsg($conn2));
    }

    if (!odbc_execute($qry, array($acc))) {
        die("Execution of the statement failed: " . odbc_errormsg($conn2));
    }

    while ($res = odbc_fetch_array($qry)) {
        $dos = $res['c_date_of_sale'];
        $c_account_no = $res['c_account_no'];
        $c_last_name = $res['c_b1_last_name'];
        $c_first_name = $res['c_b1_first_name'];
        $c_middle_name = $res['c_b1_middle_name'];
        $c_tel_no = $res['c_tel_no'];
        $c_mobile_no = $res['c_mobile_no'];
        $c_email = $res['c_email'];
        $c_address = $res['c_address'];
        $c_city_prov = $res['c_city_prov'];
        $c_zip_code = $res['c_zip_code'];
        
    }
}

    $sql2 = "SELECT * FROM t_bci WHERE c_account_no = ? ORDER BY c_last_updated DESC";

   // Execute the SQL query
  // Prepare the SQL query
    $qry2 = odbc_prepare($conn2, $sql2);
    if (!$qry2) {
        die("Preparation of the statement failed: " . odbc_errormsg($conn2));
    }

    // Execute the SQL query
    if (!odbc_execute($qry2,array($c_account_no))) {
        die("Execution of the statement failed: " . odbc_errormsg($conn2));
    }

    // Fetch the results into an array
    $items = [];
    while ($row = odbc_fetch_array($qry2)) {
        $items[] = $row;
    }
    if (!empty($items)) {
        $current_row = reset($items);
        
    }
    
    // Close the connection
    odbc_close($conn2);

    // Optional: Display the fetched data for debugging purposes
    foreach ($items as $item) {
         }
?>
 <style>
    .table3 td, .table th {
        padding: 0.25rem; /* Reduce cell padding */
        font-size: 0.875rem; /* Make font size smaller */
    }
    .table3 tr, .table tr {
        padding: 0.25rem; /* Reduce cell padding */
        font-size: 0.875rem; /* Make font size smaller */
    }
</style>

<form action="" id="update-bci" class="container">
    <div class="form-section mb-12">
        <div class="row">  
        <h5 class="text-green h6">BCI History</h5>
        <div class="table-responsive">
        <table class="table3 table-bordered table-striped">
            <thead>
                <tr>
                    <th class="text-center">Last Updated</th>
                    <th class="text-center">BCI Type</th>
                    <th class="text-center">Mobile</th>
                    <th class="text-center">Landline</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Address</th>
                    <th class="text-center">Rep. Name</th>
                    <th class="text-center">Rep. Mobile No.</th>
                    <th class="text-center">Rep. Tel No.</th>
                    <th class="text-center">Rep. Email</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="9" class="text-center">No BCI history</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <tr>  
                            <td><?php echo htmlspecialchars($item['c_last_updated']); ?></td>
                           
                            <td><?php echo $item['c_bci_type'] == 1 ? 'Outside' : 'Inside'; ?></td>
                            <td><?php echo htmlspecialchars($item['c_mobile_no']); ?></td>
                            <td><?php echo htmlspecialchars($item['c_tel_no']); ?></td>
                            <td><?php echo htmlspecialchars($item['c_email']); ?></td>
                            <td><?php echo htmlspecialchars($item['c_address'] . ' '. $item['c_city_prov'] . ' '. $item['c_zipcode'])?></td>
                            <td><?php echo htmlspecialchars($item['c_rep_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['c_rep_mobile']); ?></td>
                            <td><?php echo htmlspecialchars($item['c_rep_landline']); ?></td>
                            <td><?php echo htmlspecialchars($item['c_rep_email']); ?></td>
                         
                           
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
            <div class="container">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="c_encoded_date">BCI Date</label>
                        <input type="date" class="form-control" id="c_encoded_date" name="c_encoded_date" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="c_bci_type">BCI from Outside?</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="c_bci_type" name="c_bci_type" value="<?= $item['c_bci_type'] == 1 ? 'checked' : '' ?>>
                            <label class="form-check-label" for="c_bci_type">Yes</label>
                        </div>
                    </div>
                </div>
                <h5 class="text-green h6"> Primary Contact </h5>
                <hr>
              
                <div class="row mb-3">
                    <input type="hidden" class="form-control" id="c_account_no" name="c_account_no" value="<?= $c_account_no ?> " >
                  
                    <div class="col-md-4">
                        <label for="c_mobile_no">Mobile No.</label>
                        <input type="text" class="form-control" id="c_mobile_no" name="c_mobile_no" value="<?= htmlspecialchars($c_mobile_no) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="c_tel_no">Landline No.</label>
                        <input type="text" class="form-control" id="c_tel_no" name="c_tel_no" value="<?= htmlspecialchars($c_tel_no) ?>" >
                    </div>
                    <div class="col-md-4">
                        <label for="c_email">Email Address</label>
                        <input type="text" class="form-control" id="c_email" name="c_email" value="<?= htmlspecialchars($c_email) ?>">
                    </div>
                </div>
                <div class="row mb-3">
                   
                    <div class="col-md-12">
                        <label for="c_address">Primary Address</label>
                        <input type="text" class="form-control" id="c_address" name="c_address" value="<?= htmlspecialchars($c_address) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="c_city_prov">City Province</label>
                        <input type="text" class="form-control" id="c_city_prov" name="c_city_prov" value="<?= htmlspecialchars($c_city_prov) ?>" >
                    </div>
                    <div class="col-md-4">
                        <label for="c_zip_code">Zip Code</label>
                        <input type="text" class="form-control" id="c_zip_code" name="c_zipcode" value="<?= htmlspecialchars($c_zip_code) ?>" >
                    </div>
                </div>
                
                <h5 class="text-green h6"> Representative Contact </h5>
                <hr>
                <div class="row mb-3">
                <input type="hidden" class="form-control" id="c_last_updated" name="c_last_updated" value="<?php echo isset($current_row['c_last_updated']) ? htmlspecialchars($current_row['c_last_updated'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                <div class="col-md-12">
                    <label for="c_rep_name">Representative Name:</label>
                    <input type="text" class="form-control" id="c_rep_name" name="c_rep_name" value="<?php echo isset($current_row['c_rep_name']) ? htmlspecialchars($current_row['c_rep_name'], ENT_QUOTES, 'UTF-8') : ''; ?>" placeholder="Enter representative name">
                </div>
                <div class="col-md-4">
                    <label for="c_rep_tel_no">Rep. Landline No:</label>
                    <input type="text" class="form-control" id="c_rep_tel_no" name="c_rep_tel_no" value="<?php echo isset($current_row['c_rep_landline']) ? htmlspecialchars($current_row['c_rep_landline'], ENT_QUOTES, 'UTF-8') : ''; ?>" placeholder="Enter landline number">
                </div>
                <div class="col-md-4">
                    <label for="c_rep_mobile">Rep. Mobile No:</label>
                    <input type="text" class="form-control" id="c_rep_mobile" name="c_rep_mobile" value="<?php echo isset($current_row['c_rep_mobile']) ? htmlspecialchars($current_row['c_rep_mobile'], ENT_QUOTES, 'UTF-8') : ''; ?>" placeholder="Enter mobile number">
                </div>
                <div class="col-md-4">
                    <label for="c_rep_email">Rep. Email Address:</label>
                    <input type="email" class="form-control" id="c_rep_email" name="c_rep_email" value="<?php echo isset($current_row['c_rep_email']) ? htmlspecialchars($current_row['c_rep_email'], ENT_QUOTES, 'UTF-8') : ''; ?>" placeholder="Enter email address">
                </div>
            </div>

              
              
            </div>
        </div>
    </div>
</form>

<script>
    $(function(){
        $('#update-bci').submit(function(e){
            e.preventDefault();
            var _this = $(this);
            $('.pop-msg').remove();
            var el = $('<div>');
                el.addClass("pop-msg alert");
                el.hide();
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/New_Master.php?f=update_bci",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error: err => {
                    console.log(err);
                    alert("An error occurred", 'error');
                    end_loader();
                },
                success: function(resp){
                    if (resp.status == 'success'){
                        alert_toast(resp.msg, 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else if (!!resp.msg){
                        el.addClass("alert-danger");
                        el.text(resp.msg);
                        _this.prepend(el);
                    } else {
                        el.addClass("alert-danger");
                        el.text("An error occurred due to unknown reason.");
                        _this.prepend(el);
                    }
                    el.show('slow');
                    $('html, body, .modal').animate({scrollTop:0}, 'fast');
                    end_loader();
                }
            });
        });
    });
</script>

