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
