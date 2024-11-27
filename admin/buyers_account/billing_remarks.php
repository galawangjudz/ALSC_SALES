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
        $c_account_no = strval($res['c_account_no']);
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

    $sql2 = "SELECT * FROM t_billing_remarks WHERE c_account_no = ? ";

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
  
    while ($row = odbc_fetch_array($qry2)) {
        
        $b_remarks = $row['c_remarks'];
    }
    
    
    // Close the connection
    odbc_close($conn2);

?>


<form action="" id="update-br" class="container">
        <div class="form-group">
            <input type="hidden" class="form-control" id="buyer_acc_no" name="buyer_acc_no" value="<?= isset($c_account_no) ? $c_account_no : "" ?>" readonly>
                                   
            <label for="billing_remark">Billing Remarks</label>
            </form><textarea class="form-control" rows="10" cols="50" id="billing_remark" name="billing_remark"><?= isset($b_remarks) ? $b_remarks : '' ?></textarea>

        </div>
</form>
<script>
    $(function(){
        $('#update-br').submit(function(e){
            e.preventDefault();
            var _this = $(this);
            $('.pop-msg').remove();
            var el = $('<div>');
                el.addClass("pop-msg alert");
                el.hide();
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/New_Master.php?f=update_billing_remarks",
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

