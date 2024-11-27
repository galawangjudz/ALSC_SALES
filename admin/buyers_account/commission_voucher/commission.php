<?php     


        function get_site_name($code, $conn) {
        // Prepare the SQL query
        $code = str_replace("'", "''", $code);
        $sql = sprintf("SELECT c_name FROM t_projects WHERE c_code = '%s'", $code);
        // Execute the query
        $result = odbc_exec($conn, $sql);
    
        if (!$result) {
            // Handle query execution errors
            return 'Error executing query: ' . odbc_errormsg($conn);
        }
        
        // Fetch the result
        if (odbc_fetch_row($result)) {
            // Retrieve the column value
            $c_name = odbc_result($result, 'c_name');
            return $c_name;
        } else {
            return 'None';
        }
    }
?>

<?php


$l_acct_no = isset($_GET["acct_no"]) ? $_GET["acct_no"] : '' ;

$l_agent_data = [];

if ($l_acct_no != ''){
    $l_find = $l_acct_no;

	$sql = "SELECT * FROM t_buyers_account where c_account_no = '%s' order by c_account_no ";
	$query = sprintf($sql, $l_find);
	$l_qry = odbc_exec($conn, $query);
	$l_rst = odbc_fetch_array($l_qry);
    if ($l_rst === false) {
        echo "Error: No account found with the provided account number.";
    } else {
        // Process the result as needed
        
    
	$sql2 = "SELECT * FROM t_payment WHERE c_account_no = '%s' AND c_status = 'ERN' ORDER BY c_payment_count ";
	$query2 = sprintf($sql2, $l_find);
	$l_qry1 = odbc_exec($conn, $query2);
	$l_rst1 = odbc_fetch_array($l_qry1);
	$row_count = 0;
	while (odbc_fetch_row($l_qry1)) {
		$row_count++;
	}

	$l_reservation = $l_rst['c_reservation'];
	if ($row_count > 0) {
		$l_earnest = $l_rst['c_reservation'];
		$total_paid = 0; // Initialize a variable to store the total paid amount
		for ($i = 0; $i < $row_count; $i++) {
			$l_rst1 = odbc_fetch_array($l_qry1); // Fetch the next row as an associative array
			if ($l_rst1) {
				$total_paid += intval($l_rst1['c_amount_paid']);
			}
		}
		$l_reservation = $l_earnest + $total_paid;
	}

    $l_account_no = $l_rst['c_account_no'];
    $lot_area = $l_rst['c_lot_area'];
    $flr_area = $l_rst['c_floor_area'];
    $lot_price_sqm = $l_rst['c_price_sqm'];
    $hse_price_sqm = $l_rst['c_house_price_sqm'];
    $lot_discount = $l_rst['c_lot_discount'];
    $lot_disc_amt = $l_rst['c_lot_discount_amount'];
    $hse_discount = $l_rst['c_h_discount'];
    $hse_disc_amt = $l_rst['c_h_discount_amount'];
    $tcp_disc_amt = $l_rst['c_tcp_discount_amount'];
    $tcp_discount = $l_rst['c_tcp_discount'];
    $l_location = get_site_name(substr($l_account_no, 0, 3),$conn) . ' ' . substr($l_account_no, 3, 3) . ' ' . substr($l_account_no, 6, 2);
	$l_name = $l_rst['c_b1_last_name'] . ', ' . $l_rst['c_b1_first_name'] . ' ' . $l_rst['c_b1_middle_name'];
	if($l_rst['c_b2_last_name'] != ''){
		$l_name2 = $l_rst['c_b2_last_name'] . ', ' . $l_rst['c_b2_first_name'] . ' ' . $l_rst['c_b2_middle_name'];
	}
	$l_net_tcp = $l_rst['c_net_tcp'];
    $l_balance =  $l_rst['c_balance'];
	$l_down_per = $l_rst['c_down_percent'];
	$l_net_dp =  $l_rst['c_net_dp'];
	$l_status =  $l_rst['c_account_status'];
	$l_no_payment =  $l_rst['c_no_payments'];
	$l_date_of_sale = $l_rst['c_date_of_sale'];
	$l_network =  $l_rst['c_network'];
	$l_division =  $l_rst['c_division'];
	$l_remarks =  $l_rst['c_remarks'];
	

	$sql3 = "SELECT * FROM t_commission WHERE c_account_no = '%s' ORDER BY c_position";
    $query3 = sprintf($sql3, $l_find);
    $l_qry3 = odbc_exec($conn, $query3);

    $data = [];  // Initialize an array to store the final data

    while ($row = odbc_fetch_array($l_qry3)) {
        $l_acc = $row['c_account_no'];
        $l_code = strval($row['c_code']);
        $l_comm_sales = strval($row['c_net_tcp']);
        $l_amount = strval($row['c_amount']);
        $l_rate = strval($row['c_rate']);

        $sql4 = "SELECT * FROM t_agents WHERE c_code = '%s'";
        $query4 = sprintf($sql4, $l_code);
        $l_qry4 = odbc_exec($conn, $query4);

        while ($result = odbc_fetch_array($l_qry4)) {
            $l_agent_name = $result['c_last_name'] . ', ' . $result['c_first_name'] . ' '  . $result['c_middle_initial'];
            $l_tax_rate = isset($result["c_withholding_tax"]) ? $result["c_withholding_tax"] : 10;
            $l_position = $row['c_position'];
            //echo $l_position;
            if($l_position == 1) {
                $l_pos = 'AVP';
            }elseif($l_position == 2){
                $l_pos = 'JAV';
            }
            elseif($l_position == 3){
                $l_pos = 'AM';
            }
            elseif($l_position == 4){
                $l_pos = 'FM';
            }
            elseif($l_position == 5){
                $l_pos = 'SM';
            }
            elseif($l_position == 6){
                $l_pos = 'MA';
            }
            elseif($l_position == 7){
                $l_pos = 'EMP';
            }
            elseif($l_position == 8){
                $l_pos = 'SPC';
            }
            elseif($l_position == 9){
                $l_pos = 'VPS';
            }
            elseif($l_position == 10){
                $l_pos = 'DS';
            }
            elseif($l_position == 11){
                $l_pos = 'SMG';
            }
            elseif($l_position == 12){
                $l_pos = 'PC';
            }
            elseif($l_position == 13){
                $l_pos = 'PD';
            }
            else{
                $l_pos = 'N/A';
            }
               
            }

            $l_data = [$l_pos, $l_code, $l_agent_name, $l_amount, $l_rate, $l_tax_rate, $l_acc, $l_comm_sales];
            $data[] = $l_data; 
            
            $l_agent_data = [];

            foreach ($data as $row) {
                $l_agent_data[] = [
                    'pos' => $row[0],
                    'code' => $row[1],
                    'agent_name' => $row[2],
                    'amount' => $row[3],
                    'rate' => $row[4],
                    'whtax' => $row[5],
                    'acc_no' => $row[6],
                    'net_comm_sale' => $row[7]
                ];
            }
        }
        $commission_percentage = due_commission($conn, $l_account_no, $l_net_tcp, $l_down_per);
    }

    
}
    
    function due_commission($conn, $c_account_no, $net_tcp, $down_per){
        // Prepare SQL query
        $l_sql = "SELECT sum(c_principal) AS total_principal FROM t_payment WHERE c_account_no = '%s'";
        $query = sprintf($l_sql, $c_account_no);
        
        // Execute the query
        $l_qry = odbc_exec($conn, $query);
        
        if ($l_qry === false) {
            die("Query execution failed: " . odbc_errormsg($conn));
        }
    
        // Fetch the result
        $row = odbc_fetch_array($l_qry);
        if ($row === false) {
            die("Fetching result failed: " . odbc_errormsg($conn));
        }
    
        // Get the total principal
        $total_principal = $row['total_principal'];
        $total_dp = $net_tcp * ($down_per/100);
        if ($total_dp == 0) {
            $val1 = 0;
        } else {
            $val1 = (($total_principal / $total_dp) * 100);
        }
       
        //check_comm
        if ($val1 >= 10 and $val1 <= 19.99):
            $commission_for = 10;
        elseif($val1 >= 20 and $val1 <= 29.99):
            $commission_for = 20;
        elseif($val1 >= 30 and $val1 <= 39.99):
            $commission_for = 30;
        elseif($val1 >= 40 and $val1 <= 49.99):
            $commission_for = 40;
        elseif($val1 >= 50 and $val1 <= 59.99):
            $commission_for = 50;
        elseif($val1 >= 60 and $val1 <= 69.99):
            $commission_for = 60;
        elseif($val1 >= 70 and $val1 <= 79.99):
            $commission_for = 70;
        elseif($val1 >= 80 and $val1 <= 89.99):
            $commission_for = 80;
        elseif($val1 >= 90 and $val1 <= 99.99):
            $commission_for = 90;
        elseif($val1 >= 99.99):
            $commission_for = 100;
        else:
            $commission_for = 0;
        endif;

        // Process the result or return it
        return $commission_for;
    }


// Convert numbers to money format


?>
<!DOCTYPE html>
<table id="b_details">
    <tr>
        <td style="width: 15%;border-top:none;border-bottom:none;border-left:none;">
            <h4 class="text-red h6">Commission</h4>
        </td>
    </tr>
</table>

<div class="tab-pane fade show active" id="computation" role="tabpanel" aria-labelledby="computation-tab">

   

            <form action=""  id="commission">
                <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">        
                        <div class="form-group">
                            <label class="control-label" for="account_no">Account No:</label>
                            <input type="text" class="form-control" name="account_no" id="account_no" value="<?php echo isset($l_acct_no) ? $l_acct_no : '' ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">        
                        <div class="form-group">
                            <label class="control-label" for="location">Location:</label>
                            <input type="text" class="form-control" name="location" id="location" value="<?php echo isset($l_location) ? $l_location : '' ?>" readonly>    
                        </div>
                    </div>
                    <div class="col-md-6">        
                        <div class="form-group">
                            <label class="control-label" for="account_status">Account Status:</label>
                            <input type="text" class="form-control" name="account_status" id="account_status" value="<?php echo isset($l_status) ? $l_status : '' ?>" readonly>    
                        </div>
                    </div>
                    <div class="col-md-6">        
                        <div class="form-group">
                            <label class="control-label" for="date_of_sale">Date of Sale:</label>
                            <input type="text" class="form-control" name="date_of_sale" id="date_of_sale" value="<?php echo isset($l_date_of_sale) ? $l_date_of_sale : '' ?>" readonly>    
                        </div>
                    </div>
                    <div class="col-md-6">        
                        <div class="form-group">
                            <label class="control-label" for="buyer1">Buyer 1:</label>
                            <input type="text" class="form-control" name="buyer1" id="buyer1" value="<?php echo isset($l_name) ? $l_name : '' ?>" readonly>    
                        </div>
                    </div>
                    <div class="col-md-6">        
                        <div class="form-group">
                            <label class="control-label" for="buyer2">Buyer 2:</label>
                            <input type="text" class="form-control" name="buyer2" id="buyer2" value="<?php echo isset($l_name2) ? $l_name2 : '' ?>" readonly>    
                        </div>
                    </div>
                    <div class="col-md-6">        
                        <div class="form-group">
                            <label class="control-label" for="net_tcp">Net TCP:</label>
                            <input type="text" class="form-control" name="net_tcp" id="net_tcp" value="<?php echo isset($l_net_tcp) ? number_format($l_net_tcp,2) : '' ?>" readonly>    
                        </div>
                    </div>
                    <div class="col-md-6">        
                        <div class="form-group">
                            <label class="control-label" for="balance">Balance:</label>
                            <input type="text" class="form-control" name="balance" id="balance" value="<?php echo isset($l_balance) ? number_format($l_balance,2) : '' ?>" readonly>    
                        </div>
                    </div>
                </div>

                    <div class="row">
                        <button type="button" class="btn btn-primary add_commission" data-acc="<?php echo $l_acct_no ?>" data-dos ="<?php echo $l_date_of_sale ?>" href="javascript:void(0)">
                            <span class="fa fa-plus"></span> ADD 
                        </button>
                    </div>
                    <hr>
                    <div class="row">    
                        <!-- Your existing table code -->
                        <table class="table table-bordered" id="data-table" style="text-align:center;width:100%;">
                            <thead>
                                <tr>
                                    <th>Position</th>
                                    <th>Code</th>
                                    <th>Agent Name</th>
                                    <th>Net Commissionable Sales</th>
                                    <th>Commission Rate</th>
                                    <th>Commission Amount</th>
                                    <th>W.H. Rate</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($l_agent_data as $row): ?>
                                <tr>
                                    <td><?php echo $row['pos']; ?></td>
                                    <td><?php echo $row['code']; ?></td>
                                    <td><?php echo $row['agent_name']; ?></td>
                                    <td><?php echo number_format($row['net_comm_sale'],2); ?></td>
                                    <td><?php echo $row['rate']; ?></td>
                                    <td><?php echo number_format($row['amount'],2); ?></td> 
                                    <td><?php echo $row['whtax']; ?></td>
                                    <td>
                                    <div class="dropdown">
                                        <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Actions
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item edit_data" data-id="<?php echo $row['code']; ?>" data-acc="<?php echo $row['acc_no']; ?>" href="javascript:void(0)">
                                                <span class="fa fa-edit"></span> Edit
                                            </a>
                                            <a class="dropdown-item delete_data" data-id="<?php echo $row['code']; ?>" data-acc="<?php echo $row['acc_no']; ?>" href="javascript:void(0)">
                                                <span class="fa fa-trash"></span> Delete
                                            </a>
                                        </div>
                                    </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>    
                            </tbody>
                        </table>
                    </div>
            </form>
<script>

$(document).ready(function() {
	$('.add_commission').click(function(){
		uni_modal("Add New Commission","buyers_account/commission_voucher/commission_add.php?acc="+$(this).attr('data-acc')+"&dos="+$(this).attr('data-dos'),'mid-large')
	});

    $('.edit_data').click(function(){
		uni_modal("Update Commission","buyers_account/commission_voucher/commission_add.php?id="+$(this).attr('data-id')+"&acc="+$(this).attr('data-acc')+"&dos="+$(this).attr('data-dos'),'mid-large')
	})
	$('.delete_data').click(function(){
    _conf("Are you sure you want to delete this permanently?", "delete_agent_comm", [$(this).attr('data-id'),$(this).attr('data-acc')])
	})

    });
    function delete_agent_comm($id,$acc){
		start_loader();
		$.ajax({
			url: _base_url_ + "classes/Commission_Master.php?f=delete_agent_comm",
			method: "POST",
			data: {id: $id, acc: $acc},
			dataType: "json",
			error: function(err) {
				console.log(err);
				alert_toast("An error occurred.", 'error');
				end_loader();
			},
			success: function(resp) {
				if (typeof resp === 'object' && resp.status === 'success') {
					alert_toast(resp.msg, 'success');
					setTimeout(function() {
						location.reload();
					}, 2000);
				} else {
					alert_toast(resp.msg || "An error occurred.", 'error'); // Display default error message if msg is undefined
					end_loader();
				}
			}
		});
	}

</script>
