<?php
Class Master{
	private $conn;

    public function __construct() {
           // PostgreSQL connection parameters
           $dbhost = 'localhost';
           $dbport = '5432'; // default PostgreSQL port
           $dbname = 'CAR_TESTDB';
           $dbuser = 'glicelo';
           $dbpass = 'admin12345';
   
           // Establish PostgreSQL connection
           $this->conn  = pg_connect("host=$dbhost port=$dbport dbname=$dbname user=$dbuser password=$dbpass");
           if (!$this->conn ) {
               $resp['status'] = 'failed';
               $resp['msg'] = "Failed to connect to PostgreSQL database.";
               echo json_encode($resp);
               return;
           }
    }

    function save_agent() {
        // Extract POST data into variables
        extract($_POST);

        // Check if $c_code is empty
        if (empty($c_code)) {
            $resp['status'] = 'failed';
            $resp['msg'] = "Agent code is required.";
            echo json_encode($resp);
            return;
        }

        // Check if the agent code already exists
        $check_query = "SELECT * FROM t_agents WHERE c_code = $1";
        $check_stmt = pg_prepare($this->conn , "check_agent", $check_query);
        $check_result = pg_execute($this->conn , "check_agent", array($c_code));

        if (pg_num_rows($check_result) > 0) {
            // Prepare UPDATE query
            $update_query = "UPDATE t_agents 
                SET c_last_name = $1, c_first_name = $2, c_middle_initial = $3, c_nick_name = $4, 
                    c_sex = $5, c_birthdate = $6, c_birth_place = $7, c_address_ln1 = $8, c_address_ln2 = $9, 
                    c_tel_no = $10, c_civil_status = $11, c_sss_no = $12, c_tin = $13, c_status = $14, 
                    c_recruited_by = $15, c_hire_date = $16, c_position = $17, c_network = $18, c_division = $19
                WHERE c_code = $20";
            $update_stmt = pg_prepare($this->conn , "update_agent", $update_query);

            // Execute the UPDATE query
            $update_result = pg_execute($this->conn , "update_agent", array(
                $c_last_name,
                $c_first_name,
                $c_middle_initial,
                $c_nick_name,
                $c_sex,
                $c_birthdate,
                $c_birth_place,
                $c_address_ln1,
                $c_address_ln2,
                $c_tel_no,
                $c_civil_status,
                $c_sss_no,
                $c_tin,
                $c_status,
                $c_recruited_by,
                $c_hire_date,
                $c_position,
                $c_network,
                $c_division,
                $c_code // bind c_code for WHERE clause
            ));

            // Check if update was successful
            if ($update_result) {
                $resp['status'] = 'success';
                $resp['msg'] = "Agent record updated successfully.";
            } else {
                $resp['status'] = 'failed';
                $resp['msg'] = "Error updating agent record: " . pg_last_error($this->conn );
            }

        } else {
            // Perform INSERT query if c_code does not exist
            $insert_query = "INSERT INTO t_agents 
                            (c_code, c_last_name, c_first_name, c_middle_initial, c_nick_name, 
                            c_sex, c_birthdate, c_birth_place, c_address_ln1, c_address_ln2, 
                            c_tel_no, c_civil_status, c_sss_no, c_tin, c_status, 
                            c_recruited_by, c_hire_date, c_position, c_network, c_division)
                            VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, $16, $17, $18, $19, $20)";
            $insert_stmt = pg_prepare($this->conn , "insert_agent", $insert_query);

            // Execute the INSERT query
            $insert_result = pg_execute($this->conn , "insert_agent", array(
                $c_code,
                $c_last_name,
                $c_first_name,
                $c_middle_initial,
                $c_nick_name,
                $c_sex,
                $c_birthdate,
                $c_birth_place,
                $c_address_ln1,
                $c_address_ln2,
                $c_tel_no,
                $c_civil_status,
                $c_sss_no,
                $c_tin,
                $c_status,
                $c_recruited_by,
                $c_hire_date,
                $c_position,
                $c_network,
                $c_division
            ));

            // Check if insert was successful
            if ($insert_result) {
                $resp['status'] = 'success';
                $resp['msg'] = "Agent record inserted successfully.";
            } else {
                $resp['status'] = 'failed';
                $resp['msg'] = "Error inserting agent record: " . pg_last_error($this->conn );
            }
        }

        // Output response as JSON
        return json_encode($resp);

        // Close PostgreSQL connection
        pg_close($this->conn );


	}				

	function delete_agent() {
        extract($_POST);

        // Check if $id (assuming it's c_code) is empty
        if (empty($id)) {
            $resp['status'] = 'failed';
            $resp['msg'] = "Agent code is required.";
        } else {
            // Prepare DELETE query
            $delete_query = "DELETE FROM t_agents WHERE c_code = $1";
            $delete_stmt = pg_prepare($this->conn, "delete_agent", $delete_query);
    
            // Execute the DELETE query
            $delete_result = pg_execute($this->conn, "delete_agent", array($id));
    
            // Check if delete was successful
            if ($delete_result) {
                $resp['status'] = 'success';
                $resp['msg'] = "Agent record deleted successfully.";
                
            } else {
                $resp['status'] = 'failed';
                $resp['msg'] = "Error deleting agent record: " . pg_last_error($this->conn);
            }
        }
    
        // Output response as JSON
        return json_encode($resp);
    
        // Close PostgreSQL connection (if necessary)
        pg_close($$this->conn);
    }

    function save_agent_commission() {
        // Extract POST data into variables
        extract($_POST);
        $c_position = intval($c_position); // Ensure $c_position is an integer
        $c_date_of_sale = isset($_POST['c_date_of_sale']) ? $_POST['c_date_of_sale'] : '';
        $c_amount = floatval($c_amount); // Ensure $c_amount is a float
        $c_account_no = pg_escape_string($c_account_no);
        $c_sale = pg_escape_string($c_sale);
        $c_rate = floatval($c_rate); // Ensure $c_rate is a float
        $c_net_tcp = floatval($c_net_tcp); // Ensure $c_net_tcp is a float
        $c_network = pg_escape_string($c_network);
        $c_division = pg_escape_string($c_division);
        $c_account_mode = ($c_account_mode == '1') ? '1' : '0';
        $c_last_name = pg_escape_string($c_last_name);
        $c_first_name = pg_escape_string($c_first_name);
        $c_middle_initial = pg_escape_string($c_middle_initial);
        $c_code = pg_escape_string($c_code);
    
        // Check if the agent code already exists
        $check_query = "SELECT * FROM t_commission WHERE c_code = $1 and c_account_no::text = $2";
        $check_stmt = pg_prepare($this->conn, "check_comm", $check_query);
        $check_result = pg_execute($this->conn, "check_comm", array($c_code, $c_account_no));
    
        if (pg_num_rows($check_result) > 0) {
            // Prepare UPDATE query
            $update_query = "UPDATE t_commission
                SET c_position = $1, c_date_of_sale = $2, c_amount = $3, c_account_no = $4, 
                    c_sale = $5, c_rate = $6, c_net_tcp = $7, c_network = $8, c_division = $9, 
                    c_account_mode = $10, c_last_name = $11, c_first_name = $12, c_middle_initial = $13
                WHERE c_code = $14 and c_account_no = $15";
            $update_stmt = pg_prepare($this->conn, "update_comm", $update_query);
    
            // Execute the UPDATE query
            $update_result = pg_execute($this->conn, "update_comm", array(
                $c_position,
                $c_date_of_sale,
                $c_amount,
                $c_account_no,
                $c_sale,
                $c_rate,
                $c_net_tcp,
                $c_network,
                $c_division,
                $c_account_mode,
                $c_last_name,
                $c_first_name,
                $c_middle_initial,
                $c_code,
                $c_account_no // bind c_code for WHERE clause
            ));
    
            // Check if update was successful
            if ($update_result) {
                $resp['status'] = 'success';
                $resp['msg'] = "Agent record updated successfully.";
            } else {
                $resp['status'] = 'failed';
                $resp['msg'] = "Error updating agent record: " . pg_last_error($this->conn);
            }
        } else {
            // Prepare INSERT query
            $insert_query = "INSERT INTO t_commission (
                c_position, c_date_of_sale, c_amount, c_account_no, c_sale, c_rate, c_net_tcp, 
                c_network, c_division, c_account_mode, c_last_name, c_first_name, c_middle_initial, c_code
            ) VALUES (
                $1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14
            )";
            $insert_stmt = pg_prepare($this->conn, "insert_comm", $insert_query);
    
            // Execute the INSERT query
            $insert_result = pg_execute($this->conn, "insert_comm", array(
                $c_position,
                $c_date_of_sale,
                $c_amount,
                $c_account_no,
                $c_sale,
                $c_rate,
                $c_net_tcp,
                $c_network,
                $c_division,
                $c_account_mode,
                $c_last_name,
                $c_first_name,
                $c_middle_initial,
                $c_code
            ));
    
            // Check if insert was successful
            if ($insert_result) {
                $resp['status'] = 'success';
                $resp['msg'] = "Agent record inserted successfully.";
            } else {
                $resp['status'] = 'failed';
                $resp['msg'] = "Error inserting agent record: " . pg_last_error($this->conn);
            }
        }
    
        // Output response as JSON
        return json_encode($resp);
    
        // Close PostgreSQL connection
        pg_close($this->conn);
    }
    

    
    function delete_agent_comm() {
        extract($_POST);

        // Perform deletion query
        $delete_query = "DELETE FROM t_commission WHERE c_code = $1 AND c_account_no = $2";
        $delete_stmt = pg_prepare($this->conn, "delete_comm", $delete_query);
        $delete_result = pg_execute($this->conn, "delete_comm", array($id, $acc));
    
        // Prepare response
        if ($delete_result) {
            $response = array('status' => 'success', 'msg' => 'Agent record deleted successfully.');
        } else {
            $response = array('status' => 'failed', 'msg' => 'Error deleting agent record: ' . pg_last_error($this->conn));
        }
    
        // Output response as JSON
        echo json_encode($response);
  
        // Close PostgreSQL connection (if necessary)
        pg_close($this->conn);
    }
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
switch ($action) {
    case 'save_agent':
        echo $Master->save_agent();
        break;
	case 'delete_agent':
		echo $Master->delete_agent();
		break;

    case 'save_agent_commission':
        echo $Master->save_agent_commission();
        break;

    case 'delete_agent_comm':
        echo $Master->delete_agent_comm();
        break;


}
