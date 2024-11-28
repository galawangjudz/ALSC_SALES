
<?php


// Establish ODBC connection
$dsn = "PostgreSQL30"; 
$user = "postgres";    
$pass = "admin12345";    

$conn = odbc_connect($dsn, $user, $pass);

$conn2 = odbc_connect($dsn, $user, $pass);

if (!$conn) {
    die("Connection failed: " . odbc_errormsg());
}

// Get account number from the URL
$l_find = isset($_GET['acc']) ? $_GET['acc'] : '';

// Prepare SQL query
$l_sql = "SELECT * FROM t_buyers_account WHERE c_account_no = ?";
$l_qry = odbc_prepare($conn, $l_sql);
odbc_execute($l_qry, array($l_find));

$row = odbc_fetch_array($l_qry);
if ($row === false) {
    echo "Error: No account found with the provided account number.";
} else {
    // Process the row data
    $l_acct_no = $row['c_account_no'];  // No need to use str() function
    $type = $row['c_type'];
    $lot_area = $row['c_lot_area'];
    $price_sqm = $row['c_price_sqm'];
    $lot_disc = $row['c_lot_discount'];
    $lot_disc_amt = $row['c_lot_discount_amount'];
    $lres = $lot_area * $price_sqm;
    $lcp = $lres - ($lres * ($lot_disc * 0.01));

    // STATUS
    $account_status = $row['c_account_status'];
    $balance = $row['c_balance'];

    // HOUSE
    $house_model = $row['c_house_model'];
    $floor_area = $row['c_floor_area'];
    $house_price_sqm = $row['c_house_price_sqm'];
    $house_disc = $row['c_h_discount'];
    $house_disc_amt = $row['c_h_discount_amount'];
    $hres = $floor_area * $house_price_sqm;
    $hcp = $hres - ($hres * ($house_disc * 0.01));

    // PAYMENT
    $tcp = $row['c_tcp'];
    $tcp_discount = $row['c_tcp_discount'];
    $tcp_discount_amt = $row['c_tcp_discount_amount'];
    $vat_amt = $row['c_vat_amount'];
    if ($tcp != 0) {
        $vat = ($vat_amt / $tcp) * 100;
    } else {
        $vat = 0;  // Or you can display an error or set it to some default value
    }
    $net_tcp = $row['c_net_tcp'];

    // Other details
    $reservation = $row['c_reservation'];
    $payment_type1 = $row['c_payment_type1'];
    $payment_type_map1 = [
        'PD' => 'Partial DownPayment',
        'SC' => 'Spot Cash',
        'ND' => 'No DownPayment',
        'FD' => 'Full Down Payment',
    ];
    
    $payment_type1 = $row['c_payment_type1'];
    $payment_type1 = isset($payment_type_map[$payment_type1]) ? $payment_type_map1[$payment_type1] : '';

    $payment_type2 = $row['c_payment_type2'];
    $payment_type_map2 = [
        'DFC' => 'Deferred Cash Payment',
        'MA' => 'Monthly Amortization',
    ];

    $payment_type2 = $row['c_payment_type2'];
    $payment_type2 = isset($payment_type_map2[$payment_type2]) ? $payment_type_map2[$payment_type2] : '';

    // Financing & Payments
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
}

odbc_close($conn);

$conn2 = odbc_connect($dsn, $user, $pass);

if (!$conn2) {
    die("Connection failed: " . odbc_errormsg());
}

$query = sprintf("SELECT c_status_count FROM t_payment WHERE c_account_no = '%s' ORDER BY c_payment_count DESC LIMIT 1", $l_find);
$result = odbc_exec($conn2, $query);
if ($result) {
    if (odbc_fetch_row($result)) {
        $status_count = odbc_result($result, 'c_status_count');
    } else {
        echo "No records found.";
    }
} else {
    echo "Error: " . odbc_errormsg($conn2);
}
if ($account_status == 'Monthly Amortization' || $account_status == 'Deferred Cash Payment'){
    $remain_terms = $terms - $status_count; 
}
if  ($account_status == 'Partial DownPayment' ){
    $remain_no_pay = $no_payments - $status_count; 
}

odbc_close($conn2);
?>



<style>
#item-list th, #item-list td{
	padding:5px 3px!important;
}

.container-fluid p{
    margin: unset
}

.has-error .checkbox,
.has-error .checkbox-inline,
.has-error .control-label,
.has-error .help-block,
.has-error .radio,
.has-error .radio-inline,
.has-error.checkbox label,
.has-error.checkbox-inline label,
.has-error.radio label,
.has-error.radio-inline label {
    color: #a94442
}

.has-error .form-control {
    border-color: #a94442;
    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075)
}
.has-error .form-control:focus {
    border-color: #843534;
    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 6px #ce8483;
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 6px #ce8483
}
.has-error .input-group-addon {
    color: #a94442;
    background-color: #f2dede;
    border-color: #a94442
}
.has-error .form-control-feedback {
    color: #a94442
}
/* Style the tab */
.tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
  font-weight:bold;
}
/* Style the buttons that are used to open the tab content */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}
.lot_box{
  width:49%;
  height:auto;
  float:left;
  border: solid #36454F 1px;
  padding-left:15px;
  padding-right:15px;
  padding-bottom:15px;
  border-radius:5px;
}
.payment_box{
  width:49%;
  height:auto;
  float:left;
  border: solid #36454F 1px;
  padding-left:15px;
  padding-right:15px;
  padding-bottom:15px;
  padding-top:15px;
  border-radius:5px;
}
.payment_box2{
  width:49%;
  height:auto;
  float:left;
  border: solid #36454F 1px;
  padding-left:15px;
  padding-right:15px;
  padding-bottom:15px;
  padding-top:15px;
  border-radius:5px;
  margin-left:2%;
}
.add_cost_box{
  width:100%;
  height:auto;
  float:left;
  border: solid #36454F 1px;
  padding-left:15px;
  padding-right:15px;
  padding-bottom:15px;
  border-radius:5px;
  margin-top:15px;
}
.house_box{
  width:49%;
  height:auto;
  float:left;
  border: solid #36454F 1px;
  padding-left:15px;
  padding-right:15px;
  padding-bottom:15px;
  border-radius:5px;
  margin-left:2%;
}
.main_box{
  float:left;
  width:100%;
  height:auto;
  padding-left:15px;
  padding-right:15px!important;
  padding-top:15px;
  border: solid #36454F 1px;
  padding-bottom:15px;
  border-radius:5px;
}
#btnfind{
  margin-left:25px;
  font-weight:bold;
}
#invoice_totals{
  width:100%;
  margin-left:10px;
  margin-right:10px;
}
.space{
  float:left;
  width:100%;
  height:15px;
}
.hr_vertical
 {
   background-color:#C80000;
   color:#C80000;
   position:absolute;
   left:130px;
   border:2px;
 }
 .title_comment{
	color:black;
	font-weight:bold;
  font-size:20px;
  padding-top:5px;
  padding-bottom:15px;
  margin-top:5px;
}
#txtarea_comment{
  width:100%;
  border-radius:5px;
}
 .titles{
	color:black;
	font-weight:bold;
	text-align:center;
  font-size:20px;
  padding-top:5px;
  padding-bottom:5px;
  margin-top:5px;
}
.control-label{
  display: inline-block;
  width: auto;
}
input{
  font-size:15px!important;
  color: black!important;
  margin-top:-5px;
}
.hr_space{
	margin-top:18px;
	display:block;
}
.main-header{
  background-color:white;
}
.alsc{
  width:50px;
  height:100px;
  float:left;
}
.small{
  float:left;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-size:15px;
  margin-left:5px;
  color:black;
  font-weight: 500;
}
#remarks{
  width:100%;
  margin-left:10px;
  margin-right:10px;
}

.textarea {
  width: 100%;
}

#invoice_notes{
  height:125px;
}

.textarea textarea {
  width: 100%;
  padding: 10px;
  resize: none;
}
.type-title{
	float:left;
}


</style>
<body>
<div class="card card-outline rounded-0 card-maroon">
    
	<div class="card-header">
	<h3 class="card-title"><b>Property Account No#: <i><?php echo $l_acct_no;  ?> </i> </b></h3>
	</div>
	<div class="card-body">
    <div class="container-fluid">
 
    <div class="panel-heading">
        <div class="titles"><center><h5>Payment Computation<h5></center></div>
    </div>

    <form action="" id="restructuring">
             
    <div class="panel-body form-group form-group-sm">
        <div class="main_box">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="hidden" id="comm" name="comm" value="<?php echo $username; ?> restructured account with property ID # <?php echo $l_acct_no; ?>.">
                        <input type="hidden" class="form-control margin-bottom required prop-id" name="prop_id" id="prop_id" value="<?= isset($row['c_account_no']) ? $row['c_account_no'] : '' ?>">
     
                        <label class="control-label">Balance:</label>
                        <input type="text" class="form-control margin-bottom required balance" name="balance" id="balance" value="<?php echo isset($row['c_balance']) ? number_format($row['c_balance'],2) : 0; ?>" tabindex = "1" >
                    </div>
                </div>
                <div class="col-md-6">	
                    <div class="form-group">
                         <div class="form-group">
                            <label for="acc_stat">Account Status:</label>
                            <select name="acc_stat" id="acc_stat" class="form-control required acc-status" onchange="updateTextbox()">
                                <option name="payment_type1" value="Partial DownPayment" <?php echo isset($account_status) && $account_status == "Partial DownPayment" ? 'selected' : '' ?>>Partial DownPayment</option>
                                <option name="payment_type1" value="Full DownPayment" <?php echo isset($account_status) && $account_status == "Full DownPayment" ? 'selected' : '' ?>>Full DownPayment</option>
                                <option name="payment_type1" value="Monthly Amortization" <?php echo isset($account_status) && $account_status == "Monthly Amortization" ? 'selected' : '' ?>>Monthly Amortization</option>
                                <option name="payment_type1" value="Deferred Cash Payment" <?php echo isset($account_status) && $account_status == "Deferred Cash Payment" ? 'selected' : '' ?>>Deferred Cash Payment</option>
                            </select>
                        </div>

                        <div style="display: none;">
                            <input type="text" class="form-control margin-bottom required acc-status" name="hidden_acc_stat" id="hidden_acc_stat" value="<?php echo isset($account_status) ? $account_status : ''; ?>" tabindex="1">
                        </div>

                        <script>
                            function updateTextbox() {
                                // Get the selected value from the dropdown
                                var selectedValue = document.getElementById('acc_stat').value;

                                // Set the value of the hidden input field
                                document.getElementById('hidden_acc_stat').value = selectedValue;
                            }

                            // Call the function on page load to sync the dropdown and hidden input
                            document.addEventListener("DOMContentLoaded", function() {
                                updateTextbox();
                            });
                        </script>
 
                        </div>
                </div>
            </div>
            <div class="row">
              <div class="col-md-12">	
                  <div class="form-group">
                    <label class="control-label">Restructuring Date: </label>
                    <input type="date" class="form-control restructured-date" name="restructured_date" id="restructured_date" value="<?php echo date('Y-m-d'); ?>" >
                  </div>
              </div>
          </div>
    <div class="space"></div>
    <div class="payment_box" id = "p1_box">
        <div class="col-md-12"  id = "pay_type1">	
            <label class="control-label">Payment Type 1: </label>
            <div class="form-group">
            <style>
                select:invalid { color: gray; }
            </style>
            
            <select name="payment_type1" id="payment_type1" class="form-control required pay-type1" tabindex = "2">
            <?php if ($account_status == 'Monthly Amortization'): ?>
                <option name="payment_type1" value="Partial DownPayment" <?php echo isset($payment_type1) && $payment_type1 == "Partial DownPayment" ? 'selected' : '' ?> disabled style="background-color: gainsboro; color: black;">Partial DownPayment</option>
                <option name="payment_type1" value="Full DownPayment" <?php echo isset($payment_type1) && $payment_type1 == "Full DownPayment" ? 'selected' : '' ?> disabled style="background-color: gainsboro; color: black;">Full DownPayment</option>
                <option name="payment_type1" value="No DownPayment" <?php echo isset($payment_type1) && $payment_type1 == "No DownPayment" ? 'selected' : '' ?> disabled style="background-color: gainsboro; color: black;">No DownPayment</option>
              <?php elseif ($account_status == 'Reservation'): ?>
                <option name="payment_type1" value="Partial DownPayment" <?php echo isset($payment_type1) && $payment_type1 == "Partial DownPayment" ? 'selected' : '' ?>>Partial DownPayment</option>
                <option name="payment_type1" value="Full DownPayment" <?php echo isset($payment_type1) && $payment_type1 == "Full DownPayment" ? 'selected' : '' ?>>Full DownPayment</option>
                <option name="payment_type1" value="No DownPayment" <?php echo isset($payment_type1) && $payment_type1 == "No DownPayment" ? 'selected' : '' ?>>No DownPayment</option>
            <?php elseif ($account_status == 'Partial DownPayment'): ?>
                <option name="payment_type1" value="Partial DownPayment" <?php echo isset($payment_type1) && $payment_type1 == "Partial DownPayment" ? 'selected' : '' ?>>Partial DownPayment</option>
                <option name="payment_type1" value="Full DownPayment" <?php echo isset($payment_type1) && $payment_type1 == "Full DownPayment" ? 'selected' : '' ?>>Full DownPayment</option>
                <option name="payment_type1" value="No DownPayment" <?php echo isset($payment_type1) && $payment_type1 == "No DownPayment" ? 'selected' : '' ?>>No DownPayment</option>
            <?php endif; ?>
              </select>	
          </div>	
      </div>
    </div>
    <div class="payment_box2" id = "p2_box">
        <div class="col-md-12" id= "pay_type2">
            <label class="control-label">Payment Type 2: </label>
            <div class="form-group">
                <style>
                    select:invalid { color: gray; }
                </style>
                <select name="payment_type2" id="payment_type2" class="form-control required pay-type2" tabindex = "3" >
                <?php if ($account_status == 'Deferred Cash Payment'): ?>
                    <option name="payment_type2" value="Deferred Cash Payment" <?php echo isset($payment_type2) && $payment_type2 == "Deferred Cash Payment" ? 'selected' : '' ?>>Deferred Cash Payment</option>
                    <option name="payment_type2" value="Monthly Amortization" <?php echo isset($payment_type2) && $payment_type2 =! "Monthly Amortization" ? 'selected' : '' ?>>Monthly Amortization</option>
                <?php elseif ($account_status == 'Reservation'): ?>
                    <option name="payment_type2" value="Deferred Cash Payment" <?php echo isset($payment_type2) && $payment_type2 == "Deferred Cash Payment" ? 'selected' : '' ?>>Deferred Cash Payment</option>
                    <option name="payment_type2" value="Monthly Amortization" <?php echo isset($payment_type2) && $payment_type2 == "Monthly Amortization" ? 'selected' : '' ?>>Monthly Amortization</option>
                <?php elseif ($account_status == 'Monthly Amortization'): ?>
                    <option name="payment_type2" value="Monthly Amortization" <?php echo isset($payment_type2) && $payment_type2 == "Monthly Amortization" ? 'selected' : '' ?>>Monthly Amortization</option>
                    <option name="payment_type2" value="Deferred Cash Payment" <?php echo isset($payment_type2) && $payment_type2 =! "Deferred Cash Payment" ? 'selected' : '' ?> disabled style="background-color: gainsboro; color: black;">Deferred Cash Payment</option>
                <?php elseif ($account_status == 'Partial DownPayment' || $account_status == 'Full DownPayment'): ?>
                    <option name="payment_type2" value="Monthly Amortization" <?php echo isset($payment_type2) && $payment_type2 == "Monthly Amortization" ? 'selected' : '' ?>>Monthly Amortization</option>
                    <option name="payment_type2" value="Deferred Cash Payment" <?php echo isset($payment_type2) && $payment_type2 == "Deferred Cash Payment" ? 'selected' : '' ?>>Deferred Cash Payment</option>
                <?php endif; ?>
                </select>	
            </div>
        </div>
    </div>
    <div class="space"></div>
    <div class="payment_box" id="p1">
        <div class="col-md-12">
            <div class="form-group down-frm" id= "down_frm" >
                <label for="net_dp" class="control-label">Total DP Due: </label>
                <input type="text" class="form-control margin-bottom required net-dp" name="net_dp" id="net_dp" value="<?php echo isset($net_dp) ? number_format($net_dp,2) : 0; ?>" readonly>
                <label for="less_paymt_dte" class="control-label" >Less Paym't to Date : </label>
                <input type="text" class="form-control less-paymt-date" name="less_paymt_dte" id="less_paymt_dte" value="0">
                <label for="dp_bal" class="control-label">DP Balance : </label>
                <input type="text" class="form-control margin-bottom required dp-bal" name="dp_bal" id="dp_bal" value="<?php echo isset($net_dp) ? number_format($net_dp,2) : 0; ?>" readonly>
                <label for="acc_surcharge1" class="control-label" >Accrued Surcharge: </label>
                <input type="text" class="form-control margin-bottom required acc-surcharge1" name="acc_surcharge1" id="acc_surcharge1" value="0">
                <!--  <label for= "rem_dp" class="control-label">Rem. DP Term/s : </label> -->
               
                <?php if ($account_status == 'Partial DownPayment' )  : ?>
                <label class="control-label">Rem. DP Term/s : </label> <i> Remaining: <?php echo $no_payments = $remain_no_pay;  ?> </i>
                <?php else:?>
                <label class="control-label">Rem. DP Term/s : <?php echo $no_payments ?></label>
                <?php endif; ?>

                <?php
                // Ensure $monthly_down is computed based on $net_dp and $no_payments
                $monthly_down = isset($net_dp) && isset($no_payments) && $no_payments > 0 ? $net_dp / $no_payments : 0;

                // Display the HTML with the calculated value
                ?>
                <input type="text" class="form-control margin-bottom required rem-dp" name="rem_dp" id="rem_dp" value="<?php echo isset($no_payments) ? $no_payments : 0; ?>" maxlength= "2">
                <label for= "monthly_down" class="control-label" id ="mo_down_text">Monthly Down: </label>
                <input type="text" class="form-control margin-bottom required monthly-down" name="monthly_down" id="monthly_down" value="<?php echo isset($monthly_down) ? number_format($monthly_down,2) : 0; ?>"  >
                <label class="control-label">Commencing: </label>
                <input type="date" class="form-control first-down-date" name="first_down_date" id = "first_down_date" value="<?php echo isset($first_dp) ? $first_dp : ''; ?>">
                <label class="control-label">Until: </label>        
                <input type="date" class="form-control fd-date" name="fd_date" id = "fd_date" value="<?php echo isset($full_down) ? $full_down : ''; ?>">
                    
                
            </div>
        </div>
    </div>		
    <div class="payment_box2" id="p2">	
        <div class="col-md-12">
             <label class="control-label" id ="amt_tobe_financed_text" >Amount to be Financed:</label>
             <input type="text" class="form-control margin-bottom required amt-to-be-financed" name="amt_to_be_financed" id="amt_2b_financed" value="<?php echo isset($amt_fnanced) ? number_format($amt_fnanced,2) : 0; ?>"readonly>
             <label class="control-label" id="acc_int_text">Acc. Interest:</label>
             <input type="text" class="form-control margin-bottom required acc-interest" name="acc_interest" id="acc_interest" value="0">
             <label class="control-label"  id="acc_sur_text" >Acc. Surcharge:</label>
             <input type="text" class="form-control margin-bottom required acc-surcharge2" name="acc_surcharge2" id="acc_surcharge2" value="0">
             <label class="control-label"  id="adj_text" >Adj. Principal Balance:</label>
             <input type="text" class="form-control margin-bottom required adj-prin-bal" name="adj_prin_bal" id="adj_prin_bal" value="<?php echo isset($amt_fnanced) ? number_format($amt_fnanced,2) : 0; ?>" readonly>
           
             <div class="form-group monthly-frm" id = "monthly_frm">
              <?php if ($account_status == 'Monthly Amortization' || $account_status == 'Deferred Cash Payment' )  : ?>
                <label class="control-label">Terms: </label> <i> Note: Remaining Terms is <?php echo $remain_terms; ?> </i>
                <?php else:?>
                <label class="control-label">Terms:  <?php echo $remain_terms = $terms; ?></label>
                <?php endif; ?>
                <input type="text" class="form-control margin-bottom required ma-terms" name="ma_terms" id="ma_terms" value="<?php echo isset($remain_terms) ? $remain_terms : 1; ?>">
                <label for='int_rate' class="control-label" id='rate_text'>Interest Rate: </label>
                <input type="text" class="form-control margin-bottom required int-rate" name="int_rate" id="int_rate" value="<?php echo isset($interest_rate) ? $interest_rate : 0; ?>">
                <label for='fxd_factor' class="control-label" id='factor_text' >Fixed Factor: </label>
                <input type="text" class="form-control margin-bottom required fixed-factor" name="fxd_factor" id="fxd_factor" value="<?php echo isset($fixed_factor) ? number_format($fixed_factor,8) : 0; ?>" readonly>
                <label class="control-label">Monthly Payment: </label>
                <input type="text" class="form-control margin-bottom required monthly-amor" name="mo_amort" id="mo_amort" value="<?php echo isset($monthly_payment) ? number_format($monthly_payment,2) : 0; ?> "readonly>	
            </div>
            <label class="control-label" id= "start_text">Start Date: </label>	
            <input type="date" class="form-control required mo-start-date" name="start_date" id = "start_date" value="<?php echo date('Y-m-d'); ?>">
        </div>
    </div>
    </div>
	</div>
</div>

</form>

</body>
<script>
$(document).ready(function () {

// Initial setup based on payment types
<?php if ($payment_type2 == 'Deferred Cash Payment'): ?>
    $("#int_rate, #fxd_factor, #rate_text, #factor_text").val(0).hide();
<?php endif; ?>

<?php if (in_array($account_status, ['Monthly Amortization', 'Full DownPayment', 'No DownPayment'])): ?>
    $('#down_frm, #net_dp, #less_paymt_dte, #dp_bal, #acc_surcharge1, #no_payment, #monthly_down, #first_down_date, #fd_date, #p1, #p1_box').hide();
    $('#p2_box, #p2').width('65%');
<?php elseif ($account_status == 'Deferred Cash Payment'): ?>
    $('#down_frm, #net_dp, #less_paymt_dte, #dp_bal, #acc_surcharge1, #no_payment, #monthly_down, #first_down_date, #fd_date, #p1, #p1_box').hide();
    $('#p2_box, #p2').width('65%');
    $("#int_rate, #fxd_factor, #rate_text, #factor_text").val(0).hide();
<?php else: ?>
    $('#down_frm').show();
<?php endif; ?>

// Set balance values
var balance = <?php echo $balance; ?>;
<?php if (in_array($account_status, ['Monthly Amortization', 'Full DownPayment', 'Deferred Cash Payment'])): ?>
    var balance = <?php echo $balance; ?>;
    $('#amt_to_be_financed, #adj_prin_bal').val(balance);
<?php endif; ?>

// Event listeners for dynamic updates
$(document).on('change', ".acc-interest, .acc-surcharge2, .ma-terms, .adj-prin-bal, .int-rate", function () {

    $(".acc-interest, .acc-surcharge2").each(function() {
        var value = parseFloat($(this).val().replace(/,/g, ''));  // Remove commas for calculation
        if (!isNaN(value)) {
            $(this).val(value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        }
    });

    compute_adjustment_prin();
    compute_ma();

});

$(document).on('change', ".less-paymt-date, .acc-surcharge1", function () {
    
   
    var value = parseFloat($(this).val().replace(/,/g, ''));  // Remove commas for calculation
    if (!isNaN(value)) {
        $(this).val(value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    }
  
    mo_downpayment();
    auto_no_terms();
});

$(document).on('change', ".rem-dp, .first-down-date", function () {
    mo_downpayment();
    auto_no_terms();
});

$(document).on('change', ".pay-type2", payment_type2_changed);
$(document).on('change', ".pay-type1", payment_type1_changed);




function mo_downpayment(){
    var net_dp = parseFloat($('.net-dp').val().replace(/,/g, '') || 0);
    var less = parseFloat($('.less-paymt-date').val().replace(/,/g, '') || 0);
    var acc_sur1 = parseFloat($('.acc-surcharge1').val().replace(/,/g, '') || 0);
    var remaining_payments = parseFloat($('.rem-dp').val().replace(/,/g, '') || 1);
    var dp_bal = net_dp - less;
    var total_dp_bal = dp_bal + acc_sur1;

    $("#dp_bal").val(total_dp_bal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

    compute_rem_dp();
}



function compute_rem_dp() {
    var remaining_payments = parseFloat($('.rem-dp').val().replace(/,/g, '') || 1);
    var dp_balance = parseFloat($('.dp-bal').val().replace(/,/g, '') || 0);

    var monthly_down = dp_balance / remaining_payments || 0;
    $("#monthly_down").val(monthly_down.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    auto_no_terms();
}

function auto_no_terms() {
    var start_date = new Date($('.first-down-date').val() || Date.now());
    var terms = parseInt($('.rem-dp').val() || 1) - 1;

    start_date.setMonth(start_date.getMonth() + terms);
    $('#fd_date').val(start_date.toISOString().slice(0, 10));

    var next_month = new Date(start_date);
    next_month.setMonth(next_month.getMonth() + 1);
    $('#start_date').val(next_month.toISOString().slice(0, 10));
}

	
function compute_adjustment_prin(){
    var balance = $('.amt-to-be-financed').val() || 0;
    var acc_sur = $('.acc-surcharge2').val() || 0;
    var acc_int = $('.acc-interest').val() || 0;

    total =  parseFloat(balance.replace(/,/g, '')) +  parseFloat(acc_sur.replace(/,/g, '')) +  parseFloat(acc_int.replace(/,/g, ''));

    $("#adj_prin_bal").val(total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
	compute_ma();
  
  }


function compute_ma(){
    var rate = parseFloat($('.int-rate').val().replace(/,/g, '') / 1200 || 0) ;
    var terms = parseInt($('.ma-terms').val().replace(/,/g, '') || 0);
    var type = $('.pay-type2').val();
    var adj_balance = parseFloat($('.adj-prin-bal').val().replace(/,/g, '') || 0);



    if (type === "Deferred Cash Payment" ) {
        if (terms == 0){
            $("#mo_amort").val(0);
        }
        var total = adj_balance/terms;
        $("#mo_amort").val(total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

    }else{
        var total = (rate === 0 || terms === 0 || !isFinite(rate) || !isFinite(terms)) ? 0 : adj_balance / (rate / (1 - Math.pow(1 + rate, -terms)));

        var rate_factor = rate / (1 - Math.pow(1 + rate, -terms));
       
        if (total == 0 || !isFinite(total) ){
            $("#fxd_factor").val(0.00);
            $("#mo_amort").val(0.00);
        }else{
            let fxd_factor = (adj_balance / total);
            fxd_factor = parseFloat(fxd_factor);
            $("#fxd_factor").val(fxd_factor.toFixed(8));
            let gtotal = adj_balance * fxd_factor;

            // Round off to the nearest integer
            gtotal = Math.round(gtotal);
            $("#mo_amort").val(gtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
         }

    }

}




// Payment type 1 change handler
function payment_type1_changed() {
    var type = $('.pay-type1').val();

    if (type === "Full DownPayment" || type === "No DownPayment") {
        adjust_for_down_payment(type);
    } else {
        reset_payment_form();
    }
    compute_ma();
}

function payment_type2_changed() {
    var paymentType2 = $('.pay-type2').val();
    var accountStatus = $('.acc-status').val();

    $('#loan_text').text("Amount to be financed :");
    $('#int_rate, #fxd_factor, #rate_text, #factor_text, #monthly_frm').show();
    $('#ma_text').text("Monthly Amortization");

    // Adjust account status for specific conditions
    if (['Monthly Amortization', 'Deferred Cash Payment'].includes(accountStatus)) {
        $('.acc-status').val("Monthly Amortization");
    }

    // Handle "Deferred Cash Payment" logic
    if (paymentType2 === "Deferred Cash Payment") {
        $('#ma_text').text("Deferred Cash Payment");
        if (['Monthly Amortization', 'Deferred Cash Payment'].includes(accountStatus)) {
            $('.acc-status').val("Deferred Cash Payment");
        }
        $('#loan_text').text("Deferred Amount:");
        $("#int_rate, #fxd_factor").val(0).hide();
        $('#rate_text, #factor_text').hide();
    } else {
        // Reset to default view for other payment types
        $('#rate_text, #factor_text, #int_rate, #fxd_factor').show();
    }

    compute_ma();
}



function adjust_for_down_payment(type) {
    $('.acc-status').val("Reservation");

    if (type === "Full DownPayment") {
        $('#net_dp, #less_paymt_dte, #dp_bal, #monthly_down, #fd_date').show();
    } else {
        $('#down_frm').hide();
    }
}

function reset_payment_form() {
    $('#net_dp, #less_paymt_dte, #dp_bal, #monthly_down, #first_down_date, #fd_date').show();
}

// Form validation and submission
function validateForm() {
    var errorCounter = 0;

    $(".required").each(function () {
        if ($(this).val() === '') {
            $(this).parent().addClass("has-error");
            errorCounter++;
        } else {
            $(this).parent().removeClass("has-error");
        }
    });
    return errorCounter;
}

$('#restructuring').submit(function (e) {
    e.preventDefault();

    if (validateForm() > 0) {
        alert_toast("Please complete all required fields!", "warning");
        return;
    }
    start_loader();
    $.ajax({
        url: _base_url_ + "classes/New_Master.php?f=create_restructured",
        data: new FormData($(this)[0]),
        cache: false,
        contentType: false,
        processData: false,
        method: 'POST',
        dataType: 'json',
        success: function (resp) {
            if (resp.status === 'success') {
                location.reload();
            } else {
                alert_toast("An error occurred", 'error');
            }
            end_loader();
        },
        error: function (err) {
            console.log(err);
            alert_toast("An error occurred", 'error');
            end_loader();
        }
    });
});

});
</script>