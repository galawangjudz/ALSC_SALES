

<?php
 if($_settings->chk_flashdata('success')): ?>
<script>
    alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<?php
$usertype = $_settings->userdata('user_type');
$user_role = $usertype;

##ODBC_CONNECTION

$dsn = "PostgreSQL30"; 
$user = "postgres";    
$pass = "admin12345";    

$conn = odbc_connect($dsn, $user, $pass);

if (!$conn) {
    die("Connection failed: " . odbc_errormsg());
}


##### JUDZ CONNECTION ######
$dbhost = 'localhost';
$dbport = '5432'; // default PostgreSQL port
$dbname = 'CAR_TESTDB';
$dbuser = 'glicelo';
$dbpass = 'admin12345';

$cnx  = pg_connect("host=$dbhost port=$dbport dbname=$dbname user=$dbuser password=$dbpass");
if (!$cnx) {
    die("Error connecting to PostgreSQL database: " . pg_last_error());
}



$l_acct_no = isset($_GET["acct_no"]) ? $_GET["acct_no"] : '' ;


if($l_acct_no != ''){
  
    $l_find = $l_acct_no;

	$l_sql = "SELECT * FROM t_buyers_account where c_account_no = '%s' order by c_account_no ";
    $query = sprintf($l_sql, $l_find);
	$l_qry = odbc_exec($conn, $query);
	$row = odbc_fetch_array($l_qry);
    if ($row === false) {
        echo "Error: No account found with the provided account number.";
    } else {
		while (odbc_fetch_row($l_qry)):
			 ///LOT
             $l_acct_no = $row['c_account_no'];
             $type = $row['c_type'];
             $lot_area = $row['c_lot_area'];
             $price_sqm = $row['c_price_sqm'];
             $lot_disc = $row['c_lot_discount'];
             $lot_disc_amt = $row['c_lot_discount_amt'];
             $lres = $lot_area * $price_sqm;
             $lcp = $lres-($lres*($lot_disc*0.01));
     
             //HOUSE
             $house_model = $row['c_house_model'];
             $floor_area = $row['c_floor_area'];
             $house_price_sqm = $row['c_house_price_sqm'];
             $house_disc = $row['c_house_discount'];
             $house_disc_amt = $row['c_house_discount_amt'];
             $hres = $floor_area * $house_price_sqm;
             $hcp = $hres-($hres*($house_disc*0.01));
             
             //PAYMENT
             $tcp = $row['c_tcp'];
             $tcp_discount = $row['c_tcp_discount'];
             $tcp_discount_amt = $row['c_tcp_discount_amt'];
             
             $vat_amt = $row['c_vat_amount'];
             $vat =$vat_amt/$tcp * 100;
             $net_tcp = $row['c_net_tcp'];
     
             $reservation = $row['c_reservation'];
             $p1 = $row['c_payment_type1'];
             $p2 = $row['c_payment_type2'];
     
             $amt_fnanced = $row['c_amt_financed'];
             $monthly_down = $row['c_monthly_down'];
             $first_dp = $row['c_first_dp'];
             $full_down = $row['c_full_down'];
             $terms = $row['c_terms'];
             $interest_rate = $row['c_interest_rate'];
             $fixed_factor = $row['c_fixed_factor'];
             $monthly_payment = $row['c_monthly_payment'];
             $no_payments = $row['c_no_payments'];
             $net_dp = $row['c_net_dp'];
             $down_percent = $row['c_down_percent'];
             $start_date = $row['c_start_date'];
             
        
		endwhile;



	}

}
?>


<div class="card card-outline rounded-0 card-maroon">
    <div class="card-header">
        <h3 class="card-title"><b><i>Buyer's Account</b></i></h3>
    </div>
    <div class="card-body">
        <div class="pd-20">   
            <div class="container-fluid">
                <div class="container">
                    <form action="" id="filter">
                        <?php $l_acct_no  = '' ?>
                        <div class="row align-items-end">
                            <input type="hidden" id="page" name="page" value="buyers_account" class="form-control form-control-sm rounded-0">
                            <div class="col-md-3 form-group">
                                <input type="text" id="acct_no" name="acct_no" value="<?= $l_acct_no ?>" class="form-control" placeholder="Enter Account No" maxlength="11">
                            </div>
                            <div class="col-md-3 form-group">
                                <button class="btn btn-primary"><i class="fa fa-search"></i> Find Account</button>
                            </div>
                        </div>
                    </form>
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="buyer-details-tab" data-toggle="tab" href="#buyer-details" role="tab" aria-controls="buyer-details" aria-selected="true">Buyer's Profile</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="inv-val-tab" data-toggle="tab" href="#inv-val" role="tab" aria-controls="inv-val" aria-selected="false">Investment Value</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="computation-tab" data-toggle="tab" href="#computation" role="tab" aria-controls="computation" aria-selected="false">Payment Computation</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="payment-record-tab" data-toggle="tab" href="#payment-record" role="tab" aria-controls="payment-record" aria-selected="false">Payment Record</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="due-record-tab" data-toggle="tab" href="#due-record" role="tab" aria-controls="due-record" aria-selected="false">Due/Over Due Details</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="commission-tab" data-toggle="tab" href="#commission" role="tab" aria-controls="commission" aria-selected="false">Commission</a>
                        </li>
                        
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="buyer-details" role="tabpanel" aria-labelledby="buyer-details-tab">
                            <div class="card mt-3">
                                <table id="b_details">
                                    <tr>
                                        <td style="width: 15%;border-top:none;border-bottom:none;border-left:none;">
                                            <h4 class="text-red h6">Buyer's Profile</h4>
                                            
                                        </td>
                                        <td></td>
                                        <td style="width: 15%;border-top:none;border-bottom:none;border-left:none;">
                                       
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-block btn-sm btn-danger btn-flat border-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-cogs"></i> Settings 
                                                </button>
                                                <div class="dropdown-menu"> 
                                                    <a class="dropdown-item update_bci" data-acc="<?php echo $row['c_account_no']; ?>" href="javascript:void(0)">Update BCI</a>
                                                    <a class="dropdown-item set_retention" data-acc="<?php echo $row['c_account_no']; ?>" href="javascript:void(0)">Set Retention</a>
                                                    <a class="dropdown-item account_rest" data-acc="<?php echo $row['c_account_no']; ?>" href="javascript:void(0)">Account Restructuring</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item billing_remark" data-acc="<?php echo $row['c_account_no']; ?>" href="javascript:void(0)">Billing Remarks</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item additional_inv" data-acc="<?php echo $row['c_account_no']; ?>" href="javascript:void(0)">Add to Additional Inventory</a>
                                                </div>
                                            </div>
                                       
                                        </td>
                                    </tr>
                                </table>
                               
                                <hr>
                                <div class="container">
                                <form class="row g-3" id="buyerForm">
                                    <div class="col-md-4">
                                        <label for="acc_no" class="form-label">Account No.</label>
                                        <input type="text" class="form-control txt" id="buyer_acc_no" name="buyer_acc_no" value="<?= isset($row['c_account_no']) ? $row['c_account_no'] : '' ?>" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="date_of_sale" class="form-label">Date of Sale</label>
                                        <input type="text" class="form-control txt" id="buyer_date_of_sale" name="buyer_date_of_sale" value="<?= isset($row['c_date_of_sale']) ? $row['c_date_of_sale'] : '' ?>" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="acc_status" class="form-label">Account Status</label>
                                        <input type="text" class="form-control txt" id="buyer_acc_status" name="buyer_acc_status" value="<?= isset($row['c_account_status']) ? $row['c_account_status'] : '' ?>" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="lname" class="form-label">Last Name</label>
                                        <input type="text" class="form-control txt" id="buyer_lname" name="buyer_lname" value="<?= isset($row['c_b1_last_name']) ? $row['c_b1_last_name'] : '' ?>" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="fname" class="form-label">First Name</label>
                                        <input type="text" class="form-control txt" id="buyer_fname" name="buyer_fname" value="<?= isset($row['c_b1_first_name']) ? $row['c_b1_first_name'] : '' ?>" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="mname" class="form-label">Middle Name</label>
                                        <input type="text" class="form-control txt" id="buyer_mname" name="buyer_mname" value="<?= isset($row['c_b1_middle_name']) ? $row['c_b1_middle_name'] : '' ?>" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="bal" class="form-label">Balance</label>
                                        <input type="text" class="form-control txt" id="buyer_bal" name="buyer_bal" value="<?= isset($row['c_balance']) ? number_format($row['c_balance'], 2) : '' ?>" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="tcp" class="form-label">Net TCP</label>
                                        <input type="text" class="form-control txt" id="buyer_tcp" name="buyer_tcp" value="<?= isset($row['c_net_tcp']) ? number_format($row['c_net_tcp'], 2) : '' ?>" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="ret" class="form-label">Account Option</label>
                                        <input type="text" class="form-control txt" id="buyer_ret" name="buyer_ret" value="<?= isset($row['c_retention']) && $row['c_retention'] == 1 ? 'Retention' : '' ?>" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="text" class="form-control txt" id="buyer_email" name="buyer_email" value="<?= isset($row['c_email']) ? $row['c_email'] : '' ?>" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="mobile" class="form-label">Mobile #</label>
                                        <input type="text" class="form-control txt" id="buyer_mobile" name="buyer_mobile" value="<?= isset($row['c_mobile_no']) ? $row['c_mobile_no'] : '' ?>" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" class="form-control txt" id="buyer_title" name="buyer_title" value="" readonly>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" class="form-control txt" id="buyer_address" name="buyer_address" value="<?= isset($row['c_address']) && isset($row['c_city_prov']) && isset($row['c_zip_code']) ? $row['c_address'] . ', ' . $row['c_city_prov'] . ' ' . $row['c_zip_code'] : '' ?>" readonly>
                                    </div>
                                   
                                    <div class="col-md-12">
                                        <label for="remarks" class="form-label">
                                            Remarks
                                            <span class="rem_note">
                                                (<span class="note">NOTE:</span> The Enter key is enabled only on the last line)
                                            </span>
                                        </label>
                                        <textarea class="form-control txt" rows="10" cols="50" id="buyer_remarks" name="buyer_remarks"><?= isset($row['c_remarks']) ? $row['c_remarks'] : '' ?></textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update Remarks</button>
                                    </div>
                                </form>

                                <style>
                                        /* Style for making the checkboxes appear in two rows */
                                        .checkbox-container {
                                            display: grid;
                                            grid-template-columns: repeat(2, 1fr); /* Two columns */
                                            gap: 10px; /* Space between the checkboxes */
                                            margin-top: 10px;
                                        }

                                        .checkbox-container label {
                                            display: flex;
                                            align-items: center; /* Align the checkbox and label text */
                                            margin-bottom: 10px;
                                        }
                                    </style>
                                    <?php
                                        if (isset($_POST['update_doc'])) {
                                            // Get the account number from the form
                                            $account_number = $_POST['acct_num'];

                                            // Set checkboxes to '1' if checked, else '0'
                                            $DAS = isset($_POST['DAS']) ? 1 : 0;
                                            $CTS = isset($_POST['CTS']) ? 1 : 0;
                                            $CSR = isset($_POST['CSR']) ? 1 : 0;
                                            $RA = isset($_POST['RA']) ? 1 : 0;
                                            $DOCKET = isset($_POST['DOCKET']) ? 1 : 0;
                                            $PERMIT = isset($_POST['PERMIT']) ? 1 : 0;
                                            $TCT = isset($_POST['TCT']) ? 1 : 0;

                                            // Construct the SQL update query
                                            $update_query = "UPDATE t_buyers_account 
                                            SET DAS = $DAS, 
                                                CTS = $CTS, 
                                                CSR = $CSR, 
                                                RA = $RA, 
                                                DOCKET = $DOCKET, 
                                                PERMIT = $PERMIT, 
                                                TCT = $TCT 
                                            WHERE c_account_no = '$account_number'";

                                        // Execute the query
                                        $result = odbc_exec($conn2, $update_query);

                                        if ($result) {
                                        // Refresh the page after a successful update
                                        echo "<script>alert('Records updated successfully!');</script>";
 
                                        } else {
                                        // Show the error message if something went wrong
                                        echo "Error updating record: " . pg_last_error($conn2);
                                        }

                                        }

                                        ?> 
                                   <form method="POST" action="">
                                        <div class="col-md-12">
                                            <input type="hidden" name="acct_num" id="acct_num" class="form-control" value = '<?php echo $row['c_account_no'] ?>' />
                                       
                                            <label for="documents" class="form-label">Documents</label>
                                            <div class="checkbox-container">
                                                <label><input type="checkbox" name="DAS" value="1" <?= isset($row['das']) && $row['das'] == 1 ? 'checked' : '' ?> /> &nbsp;DAS (Deed of Absolute Sale)</label>
                                                <label><input type="checkbox" name="CTS" value="1" <?= isset($row['cts']) && $row['cts'] == 1 ? 'checked' : '' ?> /> &nbsp;CTS (Contract to Sell)</label>
                                                <label><input type="checkbox" name="CSR" value="1" <?= isset($row['csr']) && $row['csr'] == 1 ? 'checked' : '' ?> /> &nbsp;CSR (Closed Sales Report) </label>
                                                <label><input type="checkbox" name="RA" value="1" <?= isset($row['ra']) && $row['ra'] == 1 ? 'checked' : '' ?> /> &nbsp;RA (Reserved Application)</label>
                                                <label><input type="checkbox" name="DOCKET" value="1" <?= isset($row['docket']) && $row['docket'] == 1 ? 'checked' : '' ?> /> &nbsp;DOCKET</label>
                                                <label><input type="checkbox" name="PERMIT" value="1" <?= isset($row['permit']) && $row['permit'] == 1 ? 'checked' : '' ?> /> &nbsp;PERMIT</label>
                                                <label><input type="checkbox" name="TCT" value="1" <?= isset($row['tct']) && $row['tct'] == 1 ? 'checked' : '' ?> /> &nbsp;TCT (Transferred Certificate of Title)</label>
                                            </div>
                                        </div>
                                       
                                           
                                        <div class="col-md-12">
                                            <button type="submit" name="update_doc" class="btn btn-primary">Update</button>
                                        </div>
                                    </form>


                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="inv-val" role="tabpanel" aria-labelledby="inv-val-tab">
                            <div class="card mt-3">
                                <!-- Investment Value content goes here -->
								<?php include ('investment_value.php'); ?>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="computation" role="tabpanel" aria-labelledby="computation-tab">
                            <div class="card mt-3">
                                <!-- Payment Computation content goes here -->
                                <?php include ('payment_computation.php'); ?>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="payment-record" role="tabpanel" aria-labelledby="payment-record-tab">
                            <div class="card mt-3">
                                <!-- Payment Record content goes here -->
                                <?php include ('payment_record.php'); ?>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="due-record" role="tabpanel" aria-labelledby="due-record-tab">
                            <div class="card mt-3">  
                                <?php include ('over_due_record.php'); ?>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="commission" role="tabpanel" aria-labelledby="commission-tab">
                            <div class="card mt-3">
                                <!-- Commission content goes here -->
                                <?php include ('commission_voucher/commission.php'); ?>
                            </div>
                        </div>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
        document.addEventListener('DOMContentLoaded', (event) => {
            // Load the last active tab from localStorage
            const activeTab = localStorage.getItem('activeTab');
            if (activeTab) {
                const activeTabLink = document.getElementById(activeTab);
                if (activeTabLink) {
                    activeTabLink.click();
                }
            }

            // Save the active tab to localStorage when a tab is clicked
            const tabLinks = document.querySelectorAll('.nav-link');
            tabLinks.forEach(tabLink => {
                tabLink.addEventListener('click', () => {
                    localStorage.setItem('activeTab', tabLink.id);
                });
            });
        });

        function handleSubmit(event, tabId) {
            event.preventDefault();
            // Handle form submission logic here
            // Keep the active tab after submission
            localStorage.setItem('activeTab', tabId);
        }
    </script>

  <script>

$(document).ready(function() {
	$('.update_bci').click(function(){
		uni_modal("BCI Update Window","buyers_account/update_bci.php?acc="+$(this).attr('data-acc'),'large')
	});
    $('.additional_inv').click(function(){
		uni_modal("Add to Additional Inventory","buyers_account/additional_inv.php?acc="+$(this).attr('data-acc'),'mid-large')
	});

    $('.billing_remark').click(function(){
		uni_modal("Billing Remarks Window","buyers_account/billing_remarks.php?acc="+$(this).attr('data-acc'),'mid-large')
	});
    $('.set_retention').click(function(){
        _conf("Are you sure you want to retention this account?","set_retention",[$(this).attr('data-acc')])
    }) 

   

    });
    function set_retention($id){
        start_loader();
        $.ajax({
            url:_base_url_+"classes/New_Master.php?f=set_retention",
            method:"POST",
            data:{id: $id},
            dataType:"json",
            error:err=>{
                console.log(err)
                alert_toast("An error occured.",'error');
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
        })
    }


    document.addEventListener("DOMContentLoaded", function() {
      // Restore active tab from local storage
      var activeTab = localStorage.getItem('activeTab');
      if (activeTab) {
        var tabElement = document.querySelector('a[href="' + activeTab + '"]');
        if (tabElement) {
          new bootstrap.Tab(tabElement).show();
        }
      }

      // Save active tab to local storage
      var tabElements = document.querySelectorAll('a[data-toggle="tab"]');
      tabElements.forEach(function(tabElement) {
        tabElement.addEventListener('shown.bs.tab', function(event) {
          var tabID = event.target.getAttribute('href');
          localStorage.setItem('activeTab', tabID);
        });
      });
    });
  </script>
