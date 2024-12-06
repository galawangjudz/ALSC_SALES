<?php
require_once('../config.php');
require_once('DBConnection.php');

class Master extends DBConnection2 {
    public function __construct() {
        parent::__construct();

        if (!$this->conn2) {
            $resp['status'] = 'failed';
            $resp['msg'] = "Failed to connect to PostgreSQL database via ODBC.";
            echo json_encode($resp);
            exit;
        }
    }

    public function delete_lot() {
        extract($_POST);

        $l_sql = "DELETE FROM t_lots WHERE c_lid = $id";
        $ret = odbc_exec($this->conn2, $l_sql);

        if ($ret) {
            $resp['status'] = 'success';
            $resp['msg'] = "Lot successfully removed.";
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = odbc_errormsg($this->conn2);
        }

        return json_encode($resp);
    }

    function save_lot() {
        extract($_POST);
    
        // Extract lot details
        $l_site = isset($_POST['project']) ? $_POST['project'] : '';
        $l_block = isset($_POST['block']) ? $_POST['block'] : '';
        $l_lot = isset($_POST['lot']) ? $_POST['lot'] : '';
        $lot_area = isset($_POST['lot_area']) ? floatval($_POST['lot_area']) : 0;
        $price_sqm = isset($_POST['price_sqm']) ? floatval($_POST['price_sqm']) : 0;
        $status = isset($_POST['status']) ? $_POST['status'] : '';
        $remarks = isset($_POST['remarks']) ? $_POST['remarks'] : '';
        $lid = isset($_POST['lid']) ? floatval($_POST['lid']) : 0;

        // Query to get c_code from t_projects based on c_acronym (since the form uses c_acronym for selection)
        $query_code = "SELECT c_code FROM t_projects WHERE c_acronym = '$l_site' LIMIT 1"; // Using $l_site as c_acronym
        $result_code = odbc_exec($this->conn2, $query_code);

        if ($result_code) {
            // Fetch the c_code from the query result
            $row = odbc_fetch_array($result_code);
            $c_code = isset($row['c_code']) ? $row['c_code'] : '';
        } else {
            $c_code = ''; // If no result, set $c_code to empty or handle as needed
        }

        // Ensure proper formatting for block and lot
        $l_block = str_pad($l_block, 3, '0', STR_PAD_LEFT);
        $l_lot = str_pad($l_lot, 2, '0', STR_PAD_LEFT);

        $concat_lid = $c_code . $l_block . $l_lot;

        /* echo "concat" . $concat_lid; */
    
        // Check if the record exists in t_lots
        $check_lot_query = "SELECT COUNT(*) as cnt FROM t_lots WHERE c_lid = '$lid'";
        $result = odbc_exec($this->conn2, $check_lot_query);
        $exists = odbc_fetch_array($result)['cnt'] > 0;
    
        if ($exists) {
            // Update the record if it exists
            $query = "UPDATE t_lots 
                      SET 
                          c_lot_area = '$lot_area', 
                          c_price_sqm = '$price_sqm',
                          c_status = '$status', 
                          c_remarks = '$remarks' 
                      WHERE c_lid = '$lid'";
        } 

        if (empty($lid)) {
            // Check if the record exists in t_lots
            $check_lot_insert = "SELECT COUNT(*) as cnt FROM t_lots WHERE c_lid = '$concat_lid'";
            $result = odbc_exec($this->conn2, $check_lot_insert);
            $exists = odbc_fetch_array($result)['cnt'] > 0;
            
            if ($exists) {
                // Record exists, send response
                $resp = array('status' => 'success', 'msg' => 'Lot already exists.');
                echo json_encode($resp);
            } else {
                // Record does not exist, proceed with insert
                $query = "INSERT INTO t_lots (c_lid, c_site, c_block, c_lot, c_lot_area, c_price_sqm, c_status, c_remarks) 
                        VALUES ('$concat_lid', '$c_code', '$l_block', '$l_lot', '$lot_area', '$price_sqm', '$status', '$remarks')";
                // Execute the insert query (e.g., using odbc_exec or your preferred method)
            }
        }
    
        // Execute query for t_lots
        $save_lot = odbc_exec($this->conn2, $query);
    
        // Extract house details
        $house_lid = isset($_POST['house_lid']) ? $_POST['house_lid'] : '';
        if (!empty($house_lid)) {
            $house_model = isset($_POST['h_house_model']) ? $_POST['h_house_model'] : '';
            $house_area = isset($_POST['h_floor_area']) ? floatval($_POST['h_floor_area']) : 0;
            $house_price_sqm = isset($_POST['h_price_sqm']) ? floatval($_POST['h_price_sqm']) : 0;
            $house_status = isset($_POST['h_status']) ? $_POST['h_status'] : '';
            $house_unit_status = isset($_POST['h_unit_status']) ? $_POST['h_unit_status'] : '';
            $house_remarks = isset($_POST['h_remarks']) ? $_POST['h_remarks'] : ''; 
    
            // Update the record for house if house_lid is provided
            $update_house = "UPDATE t_house
                             SET 
                                 c_house_model = '$house_model', 
                                 c_floor_area = '$house_area', 
                                 c_h_price_sqm = '$house_price_sqm',
                                 c_status = '$house_status', 
                                 c_unit_status = '$house_unit_status',
                                 c_remarks = '$house_remarks' 
                             WHERE c_lid = '$house_lid'";
    
            // Execute query for t_house
            $save_house = odbc_exec($this->conn2, $update_house);
        }
    
        $resp = array('status' => 'success', 'msg' => 'Lot details processed successfully.' . (!empty($house_lid) ? ' House details updated.' : ''));
        echo json_encode($resp);
    }            
}

$Master = new Master();
$action = isset($_GET['f']) ? strtolower($_GET['f']) : 'none';
switch ($action) {
    case 'delete_lot':
        echo $Master->delete_lot();
        break;

    case 'save_lot':
        echo $Master->save_lot();
        break;
}
