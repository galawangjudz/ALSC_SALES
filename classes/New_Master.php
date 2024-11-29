<?php
require_once('../config.php');
Class Master extends DBConnection2{

    public function __construct() {
        $dsn = "PostgreSQL30"; // Replace with your DSN name
        $user = "postgres";    // Replace with your database username
        $pass = "admin12345"; // Replace with your database password

        if (!isset($this->conn2)) {
            $this->conn2 = odbc_connect($dsn, $user, $pass);

            if (!$this->conn2) {
                die("ODBC Connection Failed: " . odbc_errormsg());
            }   
        }
    }

    function update_bci() {
        // Extract POST data into variables
        extract($_POST);

        $c_account_no = pg_escape_string($_POST['c_account_no']);
        $c_mobile_no = pg_escape_string($_POST['c_mobile_no']); 
        $c_tel_no = pg_escape_string($_POST['c_tel_no']);
        $c_email = pg_escape_string($_POST['c_email']);
        $c_address = pg_escape_string($_POST['c_address']);
        $c_city_prov = pg_escape_string($_POST['c_city_prov']); 
        $c_zipcode = pg_escape_string($_POST['c_zipcode']);
        $c_bci_type = isset($_POST['c_bci_type']) ? $_POST['c_bci_type'] : 0;
        $c_encoded_date = pg_escape_string($_POST['c_encoded_date']);
        $c_rep_name =  pg_escape_string($_POST['c_rep_name']);
        $c_rep_tel_no=  pg_escape_string($_POST['c_rep_tel_no']);
        $c_rep_mobile =  pg_escape_string($_POST['c_rep_mobile']);
        $c_rep_email =  pg_escape_string($_POST['c_rep_email']);
        $c_last_updated =  pg_escape_string($_POST['c_last_updated']);
      
        $today_date = date('Y-m-d');

        $update_query = "UPDATE t_buyers_account SET c_tel_no = '$c_tel_no' , c_mobile_no = '$c_mobile_no' , c_email = '$c_email', c_consent = '1', c_address = '$c_address', c_city_prov = '$c_city_prov', c_zip_code= '$c_zipcode' where c_account_no = '$c_account_no'";

        $result = odbc_exec($this->conn2, $update_query);

        if (!$result) {
                // Handle errors
        echo "Error in query execution: " . odbc_errormsg($this->conn2);
        }
        else{
            
            if ($c_encoded_date == $c_last_updated){
                $l_sql2 = "UPDATE t_bci set c_mobile_no = '$c_mobile_no', c_tel_no = '$c_tel_no', c_email = '$c_email', c_address = '$c_address', c_city_prov = '$c_city_prov', c_zipcode= '$c_zipcode', c_rep_name = '$c_rep_name', c_rep_mobile = '$c_rep_mobile', c_rep_landline = '$c_rep_tel_no', c_rep_email = '$c_rep_email' WHERE c_account_no = '$c_account_no' and c_last_updated = '$c_encoded_date'";
                $update_result = odbc_exec($this->conn2, $l_sql2);

                 if ($update_result) {
                    $resp['status'] = 'success';
                    $resp['msg'] = "BCI record updated successfully.";
                } else {
                    $resp['status'] = 'failed';
                    $resp['msg'] = "Error updated bci record: " .odbc_errormsg($this->conn2);
                }
      
            }
            else{
                $l_sql2 = "INSERT INTO t_bci (c_account_no,c_last_updated, c_mobile_no, c_tel_no, c_email, c_address, c_city_prov, c_zipcode, c_rep_name, c_rep_mobile,c_rep_landline, c_rep_email, c_encode_date, c_bci_type ) values ('$c_account_no','$c_encoded_date','$c_mobile_no', '$c_tel_no','$c_email', '$c_address','$c_city_prov','$c_zipcode','$c_rep_name','$c_rep_mobile','$c_rep_tel_no','$c_rep_email','$today_date','$c_bci_type')";
                $insert = odbc_exec($this->conn2, $l_sql2);
                if ($insert) {
                    $resp['status'] = 'success';
                    $resp['msg'] = "BCI record inserted successfully.";
                } else {
                    $resp['status'] = 'failed';
                    $resp['msg'] = "Error inserting bci record: " .odbc_errormsg($this->conn2);
                }
            }
        }
              

        // Output response as JSON
        return json_encode($resp);

        // Close PostgreSQL connection
        odbc_close($this->conn2);


	}	
    
    
    function set_retention() {
        extract($_POST);
        // Extract POST data into variables
        $l_sql = "UPDATE t_buyers_account set c_retention = '1' where c_account_no = ".$id;
		
        $ret = odbc_exec($this->conn2, $l_sql);
		if($ret){
			

			$resp['status'] = 'success';
            $resp['msg'] = " Account successfully set to Retention.";
		
		}else{
			$resp['status'] = 'failed';
			$resp['error'] =  odbc_errormsg($this->conn2);
		}
		return json_encode($resp);

    }

    function remove_retention() {
        extract($_POST);
        // Extract POST data into variables
        $l_sql = "UPDATE t_buyers_account set c_retention = '0' where c_account_no = ".$id;

        $ret = odbc_exec($this->conn2, $l_sql);
		if($ret){

			$resp['status'] = 'success';
            $resp['msg'] = " Account successfully remove to Retention.";
		
		}else{
			$resp['status'] = 'failed';
			$resp['error'] =  odbc_errormsg($this->conn2);
		}
		return json_encode($resp);

    }

    function update_billing_remarks() {
        extract($_POST);
		
        $resp = array();
        $buyer_acc_no = filter_input(INPUT_POST, 'buyer_acc_no', FILTER_SANITIZE_NUMBER_INT);
        $billing_remark = $billing_remark = isset($_POST['billing_remark']) ? htmlspecialchars(trim($_POST['billing_remark']), ENT_QUOTES, 'UTF-8') : '';

    
        // Validate that buyer_acc_no is numeric to prevent SQL injection.
        if (!is_numeric($buyer_acc_no)) {
            $resp['status'] = 'failed';
            $resp['msg'] = 'Invalid account number provided.';
            return json_encode($resp);
        }
		
        $update_query = "UPDATE t_billing_remarks SET c_remarks = '" . $billing_remark . "' WHERE c_account_no = '". $buyer_acc_no ."'";
        //echo $update_query;
        $save = odbc_exec($this->conn2, $update_query);

        if ($save) {
            $resp['status'] = 'success';
            $resp['msg'] = "Billing remarks successfully updated.";
            
        } else {
            $resp['status'] = 'failed';
            $resp['err'] = odbc_errormsg($this->conn2);
        }
		
		return json_encode($resp);
	}

    function additional_inv() {
        extract($_POST);
        $buyer_account = pg_escape_string($_POST['buyer_acc_no']);
        $type = pg_escape_string($_POST['type_of_additional']);
        $agent = pg_escape_string($_POST['agent_assigned']);
        // Extract POST data into variables
        $l_sql = "INSERT INTO t_additional_inventory values ('$buyer_account','$type','$agent');";

		
        $ret = odbc_exec($this->conn2, $l_sql);
		if($ret){
			

			$resp['status'] = 'success';
            $resp['msg'] = " Account successfully add to agent Inventory List.";
		
		}else{
			$resp['status'] = 'failed';
			$resp['error'] =  odbc_errormsg($this->conn2);
		}
		return json_encode($resp);

    }


    function create_restructured() {
        // Extract the POST data (from the form submission)
        extract($_POST);
        

        $version_id = 2;
        // Step 1: Insert into the 'requests' table
        $request = "INSERT INTO requests (
            requester_id,
            description,
            status,
            request_type,
            created_at
        ) 
        VALUES (
            1,                       -- requester_id (example)
            'Request for account restructuring', -- description (example)
            'pending',                -- status (default is 'pending' if not provided)
            'account_restructuring',  -- request_type (example)
            CURRENT_TIMESTAMP        -- created_at (auto-populated with current timestamp)
        )";

        // Execute the SQL to insert the new request
        $request_result = odbc_exec($this->conn2, $request);

        // Step 2: Get the 'request_id' of the inserted request
        // We need to fetch the sequence value using `currval()`
        // 'requests_id_seq' is the name of the sequence generated by PostgreSQL for the 'id' column of the 'requests' table
        $request_id_query = "SELECT currval('requests_id_seq') AS request_id";
        $request_id_result = odbc_exec($this->conn2, $request_id_query);

        // Fetch the result and get the request_id
        if ($request_id_result) {
            $row = odbc_fetch_array($request_id_result);
            $request_id = $row['request_id'];  // The last inserted request_id from the sequence
        } else {
            // Handle the error if currval() fails
            $resp['status'] = 'failed';
            $resp['msg'] = 'Failed to retrieve the request_id from the sequence';
            echo json_encode($resp);
            exit;
        }

     
        $approvers_sql = "SELECT role_id, approval_order FROM approvers WHERE request_type = 'account_restructuring' ORDER BY approval_order ASC";
        $approvers_result = odbc_exec($this->conn2, $approvers_sql);

      
        while ($approval = odbc_fetch_array($approvers_result)) {
            $role_id = $approval['role_id'];
            $approver_id = $approval['role_id'];
            $approval_order = $approval['approval_order'];

         
            $approval_sql = "INSERT INTO approvals (
                request_id,
                approver_id,
                role_id,
                approval_status,
                approval_order
            ) VALUES (
                '$request_id','$approver_id', '$role_id', 'pending', '$approval_order'
            )";

            // Execute the insert query
            $approval_result = odbc_exec($this->conn2, $approval_sql);

         
        }



        // Step 3: Convert all numeric fields to proper types (if they have commas)
        $net_dp = (float)str_replace(',', '', $net_dp);
        $less_paymt_dte = (float)str_replace(',', '', $less_paymt_dte);
        $acc_surcharge1 = (float)str_replace(',', '', $acc_surcharge1);
        $rem_dp = (int)str_replace(',', '', $rem_dp);
        $monthly_down = (float)str_replace(',', '', $monthly_down);
        $amt_2be_financed = (float)str_replace(',', '', $amt_2be_financed);
        $acc_surcharge2 = (float)str_replace(',', '', $acc_surcharge2);
        $acc_interest = (float)str_replace(',', '', $acc_interest);
        $adj_prin_bal = (float)str_replace(',', '', $adj_prin_bal);
        $ma_terms = (int)str_replace(',', '', $ma_terms);
        $int_rate = (float)str_replace(',', '', $int_rate);
        $fxd_factor = (float)str_replace(',', '', $fxd_factor);
        $mo_amort = (float)str_replace(',', '', $mo_amort);

        // Step 4: Insert into the 't_restructuring' table with the new 'request_id'
        $l_sql = "INSERT INTO t_restructuring (
        c_account_no,
        c_version_id,
        c_account_status,
        c_payment_type1,
        c_payment_type2,
        c_net_dp,
        c_less_dp,
        c_acc_surcharge1,
        c_no_payments,
        c_monthly_down,
        c_first_dp,
        c_full_down,
        c_amt_financed,
        c_acc_surcharge2,
        c_acc_interest,
        c_adj_prin_bal,
        c_terms,
        c_interest_rate,
        c_fixed_factor,
        c_monthly_payment,
        c_start_date,
        c_created_at,
        c_restructured_status,
        c_request_id
        ) VALUES (
        '$prop_id',              -- Account number
        '$version_id',           -- Version ID
        '$acc_stat',             -- Account status
        '$payment_type1',        -- Primary payment type
        '$payment_type2',        -- Secondary payment type
        '$net_dp',               -- Net down payment
        '$less_paymt_dte',       -- Less down payment
        '$acc_surcharge1',       -- Surcharge 1
        '$rem_dp',               -- Number of payments
        '$monthly_down',         -- Monthly down payment
        '$first_down_date',      -- First down payment
        '$fd_date',              -- Full down payment
        '$amt_2be_financed',     -- Amount financed
        '$acc_surcharge2',       -- Surcharge 2
        '$acc_interest',         -- Accrued interest
        '$adj_prin_bal',         -- Adjusted principal balance
        '$ma_terms',             -- Loan terms in months
        '$int_rate',             -- Interest rate
        '$fxd_factor',           -- Fixed factor
        '$mo_amort',             -- Monthly payment amount
        '$start_date',           -- Start date of restructuring
        CURRENT_TIMESTAMP,       -- Creation timestamp
        'pending',               -- Restructuring status (default 'pending')
        '$request_id'            -- Link to the request_id from the 'requests' table
        )";

        // Execute the SQL to insert the restructuring data
        $restructured = odbc_exec($this->conn2, $l_sql);

        // Check if the query was successful
        if ($restructured) {
            // If successful, return a success message
            $resp['status'] = 'success';
            $resp['msg'] = "Account successfully added to the restructuring list.";
        } else {
            // If an error occurred, return the error message
            $resp['status'] = 'failed';
            $resp['error'] = odbc_errormsg($this->conn2);  // Get the error message from ODBC
        }

        // Return the response as a JSON object
        echo json_encode($resp);
    }
                    
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
switch ($action) {
    case 'update_bci':
        echo $Master->update_bci();
        break;

    case 'set_retention':
        echo $Master->set_retention();
        break;

    case 'remove_retention':
        echo $Master->remove_retention();
        break;
    

    case 'update_billing_remarks':
        echo $Master->update_billing_remarks();
        break;

    case 'additional_inv':
        echo $Master->additional_inv();
        break;


    case 'create_restructured':
        echo $Master->create_restructured();
        break;


}
